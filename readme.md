T5 Rewrite
==========

Currently, this WordPress plugin creates three new rewrite tags:

- `%postformat%`: A sanitized version of the post format of the post like 
    `standard`, `aside`, `chat`, `gallery`, `link` and so on.
    
- `%tag%`: A sanitized version of the oldest tag of the post.
- `%_custom_%(fieldname)`, where `(fieldname)` is the key of a post meta field.

These tags can be extended easily with the help of an abstract class.

There is a new help tab, listing all available rewrite tags, including the
built-in tags. 

![Screen shot help](https://raw.github.com/toscho/t5-rewrite/master/screenshots/t5-rewrite-help.png)

The permalink preview shows what URL your latest post will get.  

![Screen shot preview](https://raw.github.com/toscho/t5-rewrite/master/screenshots/t5-rewrite-permalink-preview.png)