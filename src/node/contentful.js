/**
 * Created by claysissing on 09/10/2016.
 */
var contentful = require('contentful')
var markdown = require('markdown').markdown
var pug = require('pug')
var util = require('util')
var fs = require('fs')
var dateFormat = require('dateformat')
var client = contentful.createClient({
  // This is the space ID. A space is like a project folder in Contentful terms
  space: 'h0f37v2wru17',
  // This is the access token for this space. Normally you get both ID and the token in the Contentful web app
  accessToken: '82519f865d369a0ca48d3eb8edfa0d543680e34e23622a2f39068c15f94adad9'
})

var dir = './build/blog'

if (!fs.existsSync('./build')){
  fs.mkdirSync('./build')
}

if (!fs.existsSync(dir)){
  fs.mkdirSync(dir)
}

client.getEntries()
  .then(function (contentType) {
    blogEnties(contentType.items)
  })

var blogEnties = (blogsArticles) => {

  blogsArticles.forEach((article) => {
    fs.writeFile(
      dir + '/' + article.fields.slug + '.html',
      pug.renderFile('./src/node/blog.pug',
        {
          pretty: true,
          blog: {
            title: article.fields.title,
            date: dateFormat(article.fields.date, 'dd/mm/yyyy'),
            body: markdown.toHTML( article.fields.body ),
            bodySection1: markdown.toHTML( article.fields.bodySection1 ),
            bodySection2: markdown.toHTML( article.fields.bodySection2 ),
            bodySection3: markdown.toHTML( article.fields.bodySection3 ),
            bodySection4: markdown.toHTML( article.fields.bodySection4 ),
            metaTitle: article.fields.metaTitle,
            metaDescription: article.fields.metaDescription,
          }
        }
      ),
      function (err) {
        if (err) return console.log(err);
        console.log(article.fields.slug + '.html');
      }
    )
  })
}

