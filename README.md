This extension is a parser function used to show text only users with the edit right.
Use `{{#showonlytoeditors:somecontent}}`, and `somecontent` will only show for users with the edit
permission.

This extension doesn't disable the parser cache, instead it splits it between editors and
non-editors.

## Use case
We leave comments and clarifications inside the general text, that we want to show only to editors.
We used to hide them using CSS, but search engines (I'm looking at you, Google) sometimes picked
that text up even though it was hidden.

## Caveats
This extension is only useful on wikis that aren't publically-editable; in regular wikis, all users
have edit permissions.

While a more generalized version might have been useful for others, it would have some caveats:
1. It would have to disable the cache entirely
2. It could allow editors to hide text in unwanted ways - for example, an anonymous user might set
  text to show only for anonymous users.

## To do
1. Consider making the required user right configurable, so that extension users can configure what
   group to target.


