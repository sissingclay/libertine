/**
 * Created by Clay on 13/06/2015.
 */

module Libertine {

    "use strict";

    export class $ {

        elements: any;

        constructor (selector) {

            if ('querySelectorAll' in document ) {

                if (typeof selector === 'string') {
                    this.elements = document.querySelectorAll(selector);
                } else {
                    this.elements = selector;
                }

                return this;

            } else {
                return;
            }
        }

        public on (name: string, callback: any) {
            new OnEvents(name, callback, this.elements);
            return this;
        }

        public toggleClass (className: string) {
            new OnEvents(className, null, this.elements, 'toggleClass');
            return this;
        }

        public hasClass (className: string) {
            new OnEvents(className, null, this.elements, 'hasClass');
            return this;
        }

        public child (name) {
            this.elements = this.elements.querySelectorAll(name);
            return this;
        }
    }

    export class OnEvents {

        name:string;

        constructor(name:string, callback:any, elements:any, action?: string) {
            this.loopNodes(name, callback, elements, action);
        }

        loopNodes(name:string, callback:any, elements:any, action?: string) {

            var myNodeList = elements;

            switch (action) {

                case 'toggleClass' :

                    if(myNodeList.length) {
                        for (var i = 0; i < myNodeList.length; i++) {
                            new ClassActions(name, myNodeList[i]).toggleClass();
                        }

                        return;
                    }

                    new ClassActions(name, myNodeList).toggleClass();

                break;

                case 'hasClass' :
                    for (var i = 0; i < myNodeList.length; i++) {
                        new ClassActions(name, myNodeList[i]).hasClass();
                    }
                    break;

                default :
                    for (var i = 0; i < myNodeList.length; i++) {
                        if ('addEventListener' in window) {
                            myNodeList[i].addEventListener(name, callback);
                        } else {
                            //fallback to be added here
                            myNodeList[i].attachEvent('on' + name, callback);
                        }
                    }
                break;
            }
        }
    }

    export class ClassActions {

        constructor (public class_name: string, public element: any) {
            this.class_name = class_name;
            this.element    = element;
        }

        hasClass () {
            if (this.class_name) {
                return this.element.className.match(new RegExp('(\\s|^)' + this.class_name + '(\\s|$)'));
            }
            return false;
        }

        addClass () {

            if (!this.hasClass()) {
                this.element.className += " " + this.class_name;
            }
            return this;
        }

        removeClass () {

            if (this.hasClass()) {
                var reg = new RegExp('(\\s|^)' + this.class_name + '(\\s|$)');
                this.element.className = this.element.className.replace(reg, ' ');
            }
            return this;
        }

        toggleClass () {

            if (this.hasClass()) {
                this.removeClass();
            } else {
                this.addClass();
            }
        }
    }
}

var lb = function(element) {
    return new Libertine.$(element);
}



lb('.lb-head-mobile-menu').on('click', function(ele) {
    ele.preventDefault();
    lb('.lb-head-menu').toggleClass('active');
});

lb('.linksOptions > a').on('click', function(ele) {
    ele.preventDefault();
    lb(this.nextSibling.nextSibling).toggleClass('show');
});

var form_elements = document.querySelectorAll('[data-form]'),
    form          = document.querySelector('.lb-touch');

function checkoutForm (form) {

    var isValid;

    [].forEach.call(form, function (val, index) {

        if(typeof val.getAttribute('data-form') === 'string') {
            isValid = checkFormElements(val, form);
        }
    });

    return isValid;
}

function checkFormElements (val, form) {

    var input_value         = val.value,
        error_class         = 'lb-error',
        has_error_class     = hasClass(val, error_class),
        form_valid          = true;

    if (!input_value) {

        form_valid = false;

        if(!has_error_class) {
            addClass(val, error_class);
        }
    }

    if (input_value) {

        var conditional_checks = val.getAttribute('data-form');

        if (!conditional_checks) {

            if (has_error_class) {
                removeClass(val, error_class);
            }
        }

        if (conditional_checks) {

            var checks = conditional_checks.split(','),
                is_valid_email;

            checks.forEach(function (to_checks) {

                if (to_checks === 'email') {
                    is_valid_email = checkEmail(input_value);

                    if (!is_valid_email) {

                        form_valid = false;

                        if (!has_error_class) {
                            addClass(val, error_class);
                        }
                    }

                    if (is_valid_email) {

                        if (has_error_class) {
                            removeClass(val, error_class);
                        }
                    }
                }
            });
        }
    }

    return form_valid;
}

function isFormValid (form) {

    var is_for_invalid = true;

    [].forEach.call(form, function (val, index) {

        if(typeof val.getAttribute('data-form') === 'string') {

            val.addEventListener('blur', function (ele) {
                is_for_invalid = checkFormElements(val, form);
            });
        }
    });

    return is_for_invalid;
}

isFormValid(form);
form.addEventListener('submit', function(evt) {
    evt.preventDefault();
    var is_valid = checkoutForm(form);

    if(is_valid) {
        ajax().post('/test/php/email.php', form).success(function(data) {

        });
    }

    console.log('is_valid', is_valid);

});

function hasClass(el, clss) {
    return el.className && new RegExp("(^|\\s)" +
            clss + "(\\s|$)").test(el.className);
}

function addClass(el, clss) {
    el.className += " " + clss;
}

function removeClass(el, clss) {
    var reg = new RegExp('(\\s|^)' + clss + '(\\s|$)');
    el.className = el.className.replace(reg, ' ');
}

function checkEmail(email_address) {
    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    return re.test(email_address);
}

function ajax () {

    var parse = function(req) {
        var result;
        try {
            result = JSON.parse(req.responseText);
        } catch (e) {
            result = req.responseText;
        }
        return [result, req];
    };

    var xhr = function(type, url, data) {
        var methods = {
            success: function(callback) {
                this.success = callback;
                return this;
            },
            error: function(callback) {
                this.error = callback;
                return this;
            }
        };
        var XHR = XMLHttpRequest || ActiveXObject;
        var request = new XHR('MSXML2.XMLHTTP.3.0');
        request.open(type, url, true);
        request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        request.onreadystatechange = function() {
            if (request.readyState === 4) {
                if (request.status === 200) {
                    methods.success.apply(methods, parse(request));
                } else {
                    methods.error.apply(methods, parse(request));
                }
            }
        };
        request.send(data);
        return methods;
    };

    return {
        post: function(url, data) {
            return xhr('POST', url, data);
        }
    };

};
