var CACHE_STARIC_NAME = 'static-v2'
var CACHE_DYNAMIC_NAME = 'dynamic-v2'

self.addEventListener('install', (event) => {
    console.log('[Service Worker] installing serviceWorker', event);

    event.waitUntil(
        caches.open(CACHE_STARIC_NAME)
            .then(cache => {
                console.log('[Service Worker] Precaching App Shell');
                cache.addAll([
                    '/',
                    '/offline.html'
                ])
            })
    );

});

self.addEventListener('activate', (event) => {
    console.log('[Service Worker] activate serviceWorker', event);

    event.waitUntil(
        caches.keys().then(keyList => {
            return Promise.all(keyList.map(key => {
                if (key !== CACHE_DYNAMIC_NAME) {
                    console.log('[Service Worker] Remove old cache', key);
                    return caches.delete(key);
                }
            }));
        })
    );

    return self.clients.claim();
});

// The fetch handler serves responses for same-origin resources from a cache.
// If no response is found, it populates the runtime cache with the response
// from the network before returning it to the page.
self.addEventListener('fetch', event => {

    // Skip cross-origin requests, like those for Google Analytics.
    if (event.request.url.startsWith(self.location.origin)) {
        event.respondWith(
            caches.match(event.request).then(cachedResponse => {
                if (cachedResponse) {
                    return cachedResponse;
                }
                
                return fetch(event.request).then(res => {

                        return caches.open(CACHE_DYNAMIC_NAME).then(cache => {
                            cache.put(event.request.url, res.clone());
    
                            return res;
                        })
                    }).catch(_ => {});
            }).catch(_ => {})
        );
    }

    return fetch(event.request);
});
