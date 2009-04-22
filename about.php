<div style="float:right">Contact Me: <a href="http://group.ning.com/index.php?controller=person&amp;action=sendMessage&amp;screenName=singpolyma&amp;targetUrl=http://tagging.ning.com/about.php"><img style="display:block;margin-bottom:1px;" src="http://services.nexodyne.com/email/icon/rCh%2Bsjo..V6jJQ%3D%3D/hD49bwg%3D/R01haWw%3D/0/image.png" alt="Email Me" /></a>
<a style="font-size:8pt;" href="http://services.nexodyne.com/email/">email icon generator</a></div>
<h2 id="about">What's this about?</h2>
<p>Tagging is a Ning app designed to solve the problem of synonyms in folksonomy.  In a strict taxonomy we have a certain number of (usually mutually-exclusive) categories and subcategories.  Folksnomy (or tagging) is defined by an unlimited set of keywords built by the community.  Often people tag something differently when they mean the same thing (ie web2.0 or web_2.0).  The purpose of this app is to define the relationships between tags so that other apps can identify these situations and display both sets of data to the user.  Thus a tag search for 'web2.0' will also bring back things tagged 'web_2.0' and 'web20'.</p>
<h2 id="taggroups">What are TagGroups?</h2>
<p>TagGroups define a single, unique term.  For example, the <a href="/index.php/tags/web2.0?auto">Web 2.0</a> tag group identifies the term associated with 'Web 2.0'.  A title and breif definition of the term are given.  The tags that are virtually synonymous with this term are listed under 'Group Tags' and an interface for adding new tags is provided.  Additionally 'Related Tags' can be given to define parent/child/sibling term relationships and comments can be added to discuss a particular tag group.</p>
<h2 id="synonymous">What does virtually synoymous mean?</h2>
<p>Virtually synonymous means that two tags mean the same thing in practise.  Variant spellings (web2.0 and web20, or colour and color) and plurals (hack and hacks) are usually virtually synonymous.  Siblings are not virtually synonymous (cars and trucks).  A good rule of thumb is that if you would feel comfortable tagging everything that has one tag with another they are virtually synonymous.</p>
<h2 id="process">Basic API Process</h2>
<p>The general process for using the Tagging API is as follows:
<ol>
   <li>User makes a tag query</li>
   <li>App calls Tagging API to get virtually synonymous tags</li>
   <li>App shows user the result of a tag <i>union</i> between all virtually synonymous tags</li>
</ol>
</p>
<h2 id="api">Basic API Usage</h2>
<p>The basic structure for a query to the api is:<br />
http://tagging.ning.com/index.php/tags/<span style="color:red;">tag</span>?auto&amp;format=<span style="color:red;">format</span><br />
Where tag is the tag you want data for and format is the format you want the data in (xoxo or json -- json data comes in pre-padded [default], JSONP [&amp;callback], and RAW [&amp;raw], formats).  Removing the 'auto' returns the data for all tag groups containing the tag, if there is more than one.
</p>
<p>
For more advanced queries you can include:
<ul>
<li>&amp;tag to filter by related tags</li>
<li>&amp;desc to filter by description content</li>
</p>