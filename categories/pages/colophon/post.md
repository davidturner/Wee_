Pubdate: 05-08-2011 20:10
Tags: 
Desc: 
Title: Colophon
=-=-=
#[Colophon][0]

This portfolio of work and blog exists for a single goal - to give myself an online presence with which to showcase my work and share my thoughts with my fellow developers and designers.

##Web Standards

This site has been developed with HTML5 and CSS3, the next iterations of the HTML and CSS specifications, allowing me to mark up and style content without hasty hacks.

Unfortunately, certain browsers don't support many aspects of HTML5 and CSS3 (Internet Explorer, looking at you here). Fortunately JavaScript provides a simple workaround for the *important* aspects of the site.

An added bonus of using these technologies is that I am able to style my content to suit the device that is accessing the site. This means that the site fits the device, rather than making the device fit the site.

##Browser Support

The site itself is designed to work well in as broad a selection of browsers as possible. The experience across some of them will be different. Older versions of Internet Explorer don't support @media queries for example. I've also dropped support for some older browsers as the only hits I ever get from them are me *testing* the site in them. I see no reason to support something that isn't used on my site. As such, browser support stands at:

- Internet Explorer 8 and above (older versions don't visit my site)
- Firefox 3.6 and above (should work in Firefox 3 and above, but untested)
- Chrome 13 and above (and probably older versions too)
- Opera (every version I've Tried)
- Safari 5 and above

In addition to desktop browsers I have made use of @media queries to provide a suitable experience to every browser capable of using them. This includes desktops, but also expands into the area of tablet and mobile devices. Currently I've only been able to test this on iPhone, iPad, and Android devices, but it *should* carry across most competing products as well.

##Content Management

This Portfolio has been developed using a custom CMS. Whilst I am quite partial to [WordPress][1] I've decided that I wanted something *simpler*. This CMS started out as a small project I developed for use during my final year [learning logs][2], and is completely avoids any databases, using a flat-file system instead.

The content of the site is stored in [Markdown][3] files, which are loaded, parsed and displayed by the CMS. This provides a very simple way for posts to be added and edited, removing the need for an admin panel.

As an example of how content is created and how it is displayed, you can take a look at the Markdown file [for this page][4]. There are two parts to the file, divided by =\-=\-=. Elements above this divider are used for the `<title>` tag and in various aspects of `<meta>` tags. The Pubdate is also used in RSS Generation, to set the published date of the article.

The lower area is the content which is used to generate the content of the page, and is marked up entirely using the Markdown syntax. When pages are loaded this is processed and turned into clean HTML markup. This provides a clean, uncluttered, writing environment allowing me to focus on providing content, not generating markup.

There are, however, a couple of notable limitations with this kind of system:

1. Generating pages can become quite unwieldy in terms of server load
2. Generating an accurate RSS feed for such a site can become troublesome
3. Handling comments becomes *very* problematic

Handling page generation can be quite the issue, as page loading times can be adversely affected by the size of the files, or the number of them, needing to be loaded in. To prevent this I coded together a simple caching script that creates static HTML files when pages are loaded, so that visitors over the following hour don't need to wait for the files to be parsed.

The caching can be overruled, and will be if I update any of the files used for page generation. If a Markdown file is *newer* than the cached file then the cached version is rebuilt using the newer content. Cached files also expire after an hour, so new versions will be automatically created as users visit the site.

The site's RSS feed is generated in a similar fashion to that of the posts view, but on a much grander scale, as content for the 20 most recent posts in pulled in, as opposed to (currently) five used in individual categories. This results in a slightly heavier load on the server side but this is, as with page loading, mitigated somewhat by caching files. A similar approach is used for generating the site's `sitemap.xml` file, but in a more simplistic fashion as files don't need to be loaded, we just need information about URLs listed.

Finally I have managed to implement comments in a similar fashion to how posts work. These are, as with posts, stored as single markdown files. They are *not*, however, publicly viewable like the individual post files are. This is to ensure that nothing can be done with the contents of the files. Each file is saved using a timestamp for the file name, ensuring that comments are loaded sequentially.

I have also built in two methods of preventing spam from being seen on the site. These are:

1. Hidden input field
2. Akismet integration

The hidden input field is a simple trick used to prevent a lot of spambots from having their comments processed at all. This is done by having a single, hidden, field in the form. This field is *never* viewable by humans, and thus will never be filled out by someone submitting a comment. Comments that *do* fill it out can therefore be determined to be spam and are discarded.

Akismet is used to process the comments that aren't ignored. It checks comments submitted against a database of spam comments. This accurately detects the majority of spam directed towards a site, and helps protect the site against spam. Unlike the first method, I still save these comments, but they are hidden from view. This is because, occasionally, Akisment has been known to flag legitimate comments as spam. If this is the case I can manually set them to be viewed, allowing for valid comments to be saved, and spam to be safely deleted.

##Roadmap

I am planning to release this CMS in the near future, making it freely available to people who want to use it. Please bear in mind that it is aimed at the geeky types, as everything is done in code/text, there is no admin interface.

[0]: /colophon/ "Colophon"
[1]: http://wordpress.org/
[2]: http://uni.davidturner.name/
[3]: http://daringfireball.net/projects/markdown/
[4]: /pages/colophon/post.md