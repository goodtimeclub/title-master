#Title Commander#

This repo is intended as a stage for collaborative development of [Blend Integrated Marketing](http://blendimc.com)'s **Title Master** in an effort to update it for compatibility with ExpressionEngine 2.6.1+, and other commonly-used addons. Please feel free to submit a [pull request](https://github.com/goodtimeclub/title-commander/compare/) & help bring this awesome addon into play again!

+ Original documentation is now offline but a cached version can be seen [here](http://web.archive.org/web/20140728082605/http://blendimc.com/dev/addons/title-master/).
+ Current version on [devot:ee](https://devot-ee.com/add-ons/title-master) is 1.5.9
+ devot:ee [forum](https://devot-ee.com/add-ons/support/title-master/viewforum/1843) link

##Description##
Title Master allows for the auto populating of the Title and URL Title fields based on your custom fields. Use the same template syntax that you are used to, in order to create powerful, auto-created titles. Works with Structure, Matrix, Playa and other third party field types.

**Installation**
Requires EE 2.x. 
1. Upload the title_master folder to your expressionengine/system/third_party folder. 
2. Install Module, Extension and Accessory from Addon Menu.
3. Click on Title Master from the Modules List.
4. Pick a Channel to Create a Template for.

**Template Format**
Title Master uses standard EE Template formatting. You will have access to Channel Fields, Entry Meta Data, and Channel Info. 

*Channel Fields*
Your Channel fields are available for use in the Title Master Templates. You can access the channel fields by just writing {channel_field}. For Simple Relationships and Playa Fields, just write out {relationship_field} and it will return the Title of the entry related (for Playa, it will only return the first entry). You can format Date fields with standard EE Syntax, (ie. {start_date format="%M %j, %Y"}). As this time, all other fields will just return what ever raw data is stored in the Database for the field.

*Other Variables*
In addition to Custom Fields, much of the Channel and Entry meta data is available as well. Here some of the main ones.
entry_date
channel_title/channel_name
username/screen_name - Author

*Parameters*
Besides date formatting, you can also add the "words" or "chars" parameters to your field (ie. {body_text words="20"}). This will limit the output of that field to the specified number of words or characters. Date formatting on date fields is available as well.

*Filtering*
All html will be stripped from fields automatically. EE has a limit of 100 characters on the Title Field, and 75 characters on URL Title. URL Titles behave in similar fashion to standard EE functionality. They will be lowercased, spaces will be swapped out with the defined word separator ( - or _ ). All non url safe characters (ie. Anything that is not a Number, Letter, dash or underscores) will be stripped out. If there is a duplicate URL Title, a number will be appended to the end.

**Options**
Title Template/URL Title Template - Enter the template you would like Titles/URL Titles to be formatted after. You can leave either the Title or URL Title Template field blank and Title Master will not touch that field. 
Update URL Titles on Channel Entry Edit - By default, Title Master will set the URL Title on Entry Publish, but will not touch it after that. Set this field to yes to allow Title Master to update your Url Titles on Entry Edit as well.
What would you like to do to existing entries? - When you submit the Channel Template Settings Form, Title Master gives you an option to run a 1 time update of the entries in the channel you are creating/editing templates for. By default, Title Master will just leave it blank, but if you want, you can have it update just the Titles or Titles and URL Titles of Current Entries.

**Safe Cracker**
If you use this with a Safe Cracker/SAEF form you will need to set a title for new entries. Title Master will override this title, but the entry must have a title in order to be submitted.

**Accessory**
Title Master includes an accessory that will Hide the Title and URL Titles on the Publish page. If you have the "Update URL Titles on Channel Entry Edit" setting set to No, the URL Title field will display on Edit Entry, but will still be hidden on the New Entry form. 

**Examples**
*People Channel*
If you have a channel with people in it, it is annoying to have to enter a Title when it is the same information you will enter for the First and Last Name fields. Here is a quick example of templates you could use.
Title - {last_name}, {first_name} = Brown, John
URL Title - {first_name} {last_name} = john_brown

*Events*
If you have an Events Channel, you can have Title Master grab the event Name and combine it with the Date and location of the event. This is especially helpful for recurring events that only differ in the dates that they are held on.
Title - {event_name} {start_date format="%M %j"}{if end_date}-{end_date format="%M %j"}{/if} {location}
URL Title - {event_name} {start_date format="%m%j%y"}{if end_date}-{end_date format="%m%j%y"}{/if} {location}

*Testimonials*
If you have a Testimonial page you may want to grab some of the Text from the Testimonial and combine it with the Author and date to create the title.
Title - {entry_date format="%M %j"} {testimonial chars="30"} {author_name}
URL Title - {entry_date format="%m%j%y"} {testimonial chars="30"} {author_name}

