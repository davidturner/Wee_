#Wee_ CMS

Wee_ is a flat-file Content Management System. It allows you to create posts, pages, and a great many of the things that you would like to do with a site. It *doesn't* use a database. It uses a file system, much like you would use to organise documents on your computer. There is no admin interface, no usernames or passwords, and there isn't a "What You See is What You Get" interface.

Because of this you need to be used to dealing with text, with editing files and with using FTP software. It's different from any other CMS that I've used, but it was designed to be different. Sometimes different is *good*.

##A Few Disclaimers

Before we get started on playing and breaking things, I want to make a few statements, to cover myself and to help prevent unfortunate accidents:

- Wee_ is still in development, it's not 100% ready for use unless you *know* what you're doing.
- Wee_ doesn't really have themes, it's aimed at handling content, letting you focus on the pretty.
- You should, ideally, have an understanding of [Markdown][] for content formatting.
- Currently Wee_ needs to run as a domain, or subdomain. You can't run it in a folder of a site.

With that said, if you are willing to play around with things then it is definitely possible to create a functional site using Wee_. Examples of this would be the original implementation over on my [University Learning Logs][1], [my portfolio][2] and [Simon Fraser's Portfolio][3].

The original goal for Wee_ was for it to be used to create a blog, or journal, of posts in a specific order. That was the goal for my University Logs, and I feel that it worked very well.

Things have expanded quite a bit since then, but for this release I want to focus primarily on that initial goal. So there is functionality that I have intentionally not documented. For the developer types testing out the CMS, feel free to look through the code and experiment with these features at your discretion.

##Setup

Setting up Wee_ CMS is quite simple. I think it even beats WordPress's famous five minute install, it's *that* easy. The steps required for setup are:

1. Edit config.php to include your information, such as the site name, description, and author
2. Upload the entire file structure to your server using FTP
3. Open the site in your browser

Of course, to actually have your own content loading in the site requires a bit more work, but that should provide you with a functioning site.

###Potential Problems

If the site isn't loading it is likely because you are using a Mac, which by default hides files that start with a period. The result of this is that you have uploaded all the files you could see, but *not* the `.htaccess` file. Fortunately there is a workaround for this, as I have also included a file named `htaccess.txt`. Editing this on your server to `.htaccess` (without a `.txt` extension) should fix this.

If this doesn't resolve the issues you are encountering, please get in touch with me, via [Twitter][4] or [email][5] and I'll see what can be done to resolve your issues. One of the perks of testing things, sometimes they break.

##Using Wee_

Once you have Wee_ set up, there are probably 2 things that you would *like* to do:

1. Create content
2. Style your site

Both are pretty important aspects of a site, and I'll cover both in brief here to help you get started with using Wee_.

###Content Creation

Content in Wee_ is, as mentioned previously, done using a flat-file structure. In particular it is done using [Markdown][] files organised into folders. I've used Markdown due to the immense amount of flexibility that it affords to content creation.

Markdown allows content to be written in a format that can be easily parsed into HTML for a site. This means that you can *craft* your content, and get your thoughts written down, without needing to worry to any extent about marking things up correctly. For the most part it is handled for you.

One of the great things about [Markdown][] is its sheer amount of flexibility. You can write your posts without writing a single line of HTML. If you would *like* to then you are certainly able to write your posts in pure HTML. The choice is yours.

Additionally, if you want to mix HTML and [Markdown][] this is possible. Initially you couldn't use [Markdown][] inside of HTML, but thanks to the wonders of [Markdown Extra][] it is possible to do so. Simply add an attribute to the HTML element you would like to use [Markdown][] in, setting an attribute of markdown&#61;"1".

All content is treated the same in Wee_. A post is created in the same way as a page. All content lives in the folder `/categories/`. In the downloadable version you will find two folders, `journal` and `pages`.

The `journal` folder is a category of posts. Inside of this folder are all of the posts that will be viewed if you visit the `journal` page of your site. Pagination is handled for you, but you can define how many posts you want per page in the `config.php` mentioned earlier.

The `pages` folder is where all of your pages live. The downloadable version of Wee_ will have two folders inside, `home` and `colophon`. The home page is the default home page, but that can be changed in `config.php`. The `colophon` is set up to display a colophon of the site. If you keep this in the final version of any sites, you will need to add information regarding your own site.

####Content Structure

There *is* a structure to content in Wee_. Posts need to be named `post.md`, inside the appropriately titled folder. Folders should be named without using spaces, or other odd characters. There is also a structure for handling some elements of meta data, and the title of the page. It is as follows:

    Pubdate: 26-08-2011 12:27
    Tags: Tags, For, The, Post
    Desc: Post Description
    Title: Post Title

These pieces of meta data are followed by a =\-=\-=, to separate the meta information from the content of your post. To see an example of this, check out the content of [my portfolio's colophon][6]. There are a few extra options that can be set, the main one that might be of interest is:

    Author: New Author Name

In the `config.php` there is a setting for the default author of posts. Adding this to a post allows you to set an author specifically to that post. This was implemented for people who might have guest posts, or who manage sites with more than one author.

    NoComments: True

Setting this in the top area of a post that typically allows comments to mark the comments as closed. This will still show comments that have been made, but will prevent new comments from being added.

Additionally, there are some extra tags that can be used when writing posts for use in Wee_. These (currently two) extra tags are based off of some nice functionality implemented in WordPress, and cover defining a break point as an introduction and showing a "posted on" string of text. These can be used by doing the following:

- `<!--[More]-->` is used to define a break point in content, used to create an introduction on category pages. It doesn't need to be used.
- `<!--[TimeStamp]-->` is used to display the published date of posts. Uses the HTML5 `time` tag, without the `pubdate` attribute on categories, but *with* it on single page views.

####Content Extras

Obviously when it comes to posts and pages, it's usually very nice if things are more than just text, regardless of the quality of the typography. This is catered to in Wee_, and even lets you keep images organised with their posts. Images linked to in a post should be contained inside a folder called `img` that resides in the folder containing your post. When using images, their links should *not* include an opening slash, Wee_ will do everything that it can to rewrite the URL to work. This *might* not work everywhere, and is an experimental feature of Wee_.

####Organising Category Content

The *single* biggest issue with Wee_, or any flat file system, is ordering content properly. PHP, the server language powering Wee_, *is* capable of reading files, but only in alphabetical order, from one end to another.

The original solution that I came up with for this was to order content sequentially. Whilst this works, it requires a level of effort that become frustrating... it gets in the way of creating content. A better solution was needed.

In the end I settled upon a simple solution. In the same way that each post is a file referenced by the system, you can optionally create a file in each category called `index.md`. The purpose of this file is simple, to store a list of publicly available posts for the system to display. These are listed in order of most recent post, through to oldest. Each line should contain the folder name for the post.

This provides an added bonus in that you can create posts that are *part* of a category, but that are hidden from view, simply by *not* including them in the `index.md` file. This very post is an example of the concept in action. You can still view the files through a direct link, but they will not be listed on your site anywhere.t

###Content Styling

Wee_ makes use of a *very* rudimentary theming system. Themes are saved in the `-templates` folder. In this initial development release I will be sharing all three themes that I have created. These themes, and their corresponding folders, are:

- `university` is the first theme I created, based on my original University Learning Logs. It is, as a result, very rough and ready.
- `portfolio` is the theme I use on my portfolio. It's much more refined, and is also the first theme to implement commenting.
- `starkers` has it's name blatantly stolen From [Elliot Jay Stock][7]'s fantastic WordPress theme, [Starkers][8]. It is designed as a starting point for themes, and works off of the [HTML5 Boilerplate][9] for structure.

The first two of these themes are intended to be used more as a resource than as a theme for sites. For this reason the default theme is set to `starkers`. The main content of each page, parsed from the `post.md` files, will be contained inside of `article` elements.

It will probably be easier for you to look through the structure of the `starkers` theme and apply your own approach of content styling to it. This is probably the *main* area that I am looking to receive feedback on.

[1]: http://uni.davidturner.name
[2]: http://davidturner.name
[3]: http://simonf.co.uk
[4]: http://twitter.com/HerrWulf
[5]: mailto:david@davidturner.name
[6]: http://davidturner.name/pages/colophon/post.md
[7]: http://elliotjaystocks.com
[8]: http://starkerstheme.com
[9]: http://html5boilerplate.com

[Markdown]: http://daringfireball.net/projects/markdown/
[Markdown Extra]: http://michelf.com/projects/php-markdown/extra/