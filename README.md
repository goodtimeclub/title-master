#Title Commander#

This repo is intended as a stage for collaborative development of [Blend Integrated Marketing](http://blendimc.com)'s **Title Master** in an effort to update it for compatibility with ExpressionEngine 2.6.1+, and other commonly-used addons. Please feel free to submit a [pull request](https://github.com/goodtimeclub/title-commander/compare/) & help bring this awesome addon into play again!

+ Original documentation is now offline but a cached version can be seen [here](http://web.archive.org/web/20140728082605/http://blendimc.com/dev/addons/title-master/).
+ Current version on [devot:ee](https://devot-ee.com/add-ons/title-master) is 1.5.9
+ devot:ee [forum](https://devot-ee.com/add-ons/support/title-master/viewforum/1843) link

##Description##
Title Master allows for the auto populating of the Title and URL Title fields based on your custom fields. Use the same template syntax that you are used to, in order to create powerful, auto-created titles. Works with Structure, Matrix, Playa and other third party field types.

##Examples##
Change Length of Title, URL Title fields
Now you can change the length of Title and URL Title fields for your channel entries so you can have longer titles than the default 100 character limit and 75 character limit for URL Titles.

###Manage Structure URLs###
Now you can manage Structure Page URLs via Title Master! Your Url Title Template will be used to create the last segment in the Structure Pages URI.

###Bypass Title Master Auto Generation###
If you do not want Title Master to process your Title/URL Title field(s), then just leave the template field blank. A quick example might be wanting to adjust the length of the Title Field, but still wanting to enter a Title manually.

###Increment###
If you need to have an defined incrementing number in your Title/Url Title, you can now do that. Just add `{x}` where you want and the entry will include. Title Master will even allow you to enter a custom increment number to start from.
Title - `{x}` - `{last_name}`, `{first_name}` = 43 - Brown, John
URL Title -  `{first_name}` `{last_name}` `{x}` = john_brown_43

###People###
If you have a channel with people in it, it is annoying to have to enter a Title when it is the same information you will enter for the First and Last Name fields. Here is a quick example of templates you could use.
Title - `{last_name}`, `{first_name}` = Brown, John
URL Title -  `{first_name}` `{last_name}` = john_brown

###Events###
If you have an Events Channel, you can have Title Master grab the event dame and combine it with the date and location of the event. This is especially helpful for recurring events that only differ in the dates that they are held on.
Title - `{event_name}` `{start_date format=”%M %j”}{if end_date}-{end_date format=”%M %j”}{/if}` `{location}`
URL Title - `{event_name}` `{start_date format=”%m%j%y”}{if end_date}-{end_date format=”%m%j%y”}{/if}` `{location}`

###Custom Title Labels and Instructions###
If you want to set a custom label and set of instructions for the Title field, just add a custom field with the Label you want it to be (ie. Company Name) and set the field name to something like title_holder. Then you can add instructions as needed.

For the Title Master settings, just set the Title and Url Title templates to your custom field name `{title_holder}`, and your all set! Now, no more confusion over what is supposed to go into that pesky Title field.

###Testimonials###
If you have a Testimonial page you may want to grab some of the text from the testimonial and combine it with the author and date to create the title.
Title - `{entry_date format=”%M %j”} {testimonial } {author_name}`
URL Title - `{entry_date format=”%m%j%y”} {testimonial } {author_name}`

Includes full Third Party Fieldtype, Category and Foreign Characters support! Also, you can see your custom fields right from the template settings screen and click to add them to your Title/Url Title Templates.  Finally, you can also use your own Word/Character limiting plugin to make even more powerful Title/URL Title combinations.
