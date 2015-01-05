redaxo-addon-article2json
=========================

Adds optional JSON-Output to Redaxo-CMS-Sites which can be configured in the backend.<br/>


#Install
Put all files in a separate folder and copy the whole folder to the */redaxo/addons/* directory.<br/>
After that log into your backend and activate the addon.

#How-to-use
You will see a new menuitem on the sidebar which is called "REX2JSON".<br/>If you click on it you will be able to declare a specific JSON-Output to every module that is defined in your backend.<br/><br/>
**NOTE:** If there are no modules available nothing can be edited!

If there are modules available you can now press the "XML-Schema zuweisen/ändern" button for one of these. You will see an empty textarea.<br/>
Now you can add a specific JSON-Output for the selected module, e.g.:

```
{
   "image": "#if#file1####file1####if#file1#",
   "title": "#if#1####1####if#1#",
   "category": "#if#6####6####if#6#",
   "date": "###2###",
   "text": "#if#3####3####if#3#",
   "link1": "#if#4####4####if#4#",
   "link2": "#if#5####5####if#5#"
}
```

As you can see you can define JSON-attributes for every value you need to get from Redaxo. The module-values can be retrieved by using a hashtag-syntax:

```
###N### => will resolve to REX_VALUE[N]
###fileN### => will resolve to REX_FILE[N]
###linkN### => will resolve to REX_LINK[N]

#if#N# ... #if#N# => will check if the value exists, otherwise if you just resolve ###N### there may be a php-error occuring
#if#fileN# ... #if#fileN# => same as above but for file-values
```

I know that the syntax is far from intuitive - sorry, could be done better but the plugin had to be created fast.  :-)<br/>
Feel free to contribute and improve!

If you want to test your JSON-output you can then open any article which contains the selected/edited modules and just add the parameter "&asxml=1" at the end of the URL (sorry, needs to be changed to 'asjson' - the addon was first used as XML exporter).<br/> This surely only works if you call the "index.php?article_id=X" URLs and not the SEO-optimized ones.


Cheers!
Martin
