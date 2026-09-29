# Canvas block - interaction and usability

Playground environment for the moderated Canvas block sessions. It tests whether people can reorganise and build layouts with Canvas without help.

## Open the test site

[Launch in WordPress Playground](https://playground.wordpress.net/?blueprint-url=https%3A%2F%2Fraw.githubusercontent.com%2Fgetdave%2Fwordpress-user-testing%2Ftrunk%2Fcanvas-block%2Fblueprint.json)

It opens straight into the block editor on the homepage, logged in as admin. Every visit starts from a clean site, so reload the link to reset between participants.

## What's in the site

- WordPress latest (Canvas needs 7.1 or newer) on PHP 8.3
- Twenty Twenty-Five with the Morning style variation
- Canvas, pinned to a build of [Automattic/canvas](https://github.com/Automattic/canvas) `trunk` at `a815d31` (28 September 2026)
- A static homepage plus About, Workshops and Contact pages
- The header Navigation block left on its default Page List, so it lists all four pages
- Three photos in the Media Library (bluebells, meadow, hydrangea) for building layouts

## Homepage sections

The sections run top to bottom in task order. Nothing on the page names a task or mentions Canvas, so the page doesn't lead participants.

| # | Section | Block | Used for |
| --- | --- | --- | --- |
| 1 | "Seasonal flowers, arranged by hand" - image on the left, text and button on the right | Canvas | Task 1, reorganise (e.g. move the image to the right) |
| 2 | Empty grey band | Canvas (empty) | Task 2, build new |
| 3 | Empty band on the page background | Canvas (empty) | Task 3, evenly spaced row of three |
| 4 | "Saturday workshops" - image left, text right | Canvas | Task 4, container boundary |
| 5 | "Weddings and events" - image left, text right | Group containing Columns | Task 4, container boundary |

Sections 4 and 5 look the same on purpose. The only difference is how they behave, which is the point of Task 4. Give the same instruction for both, such as "put the image on the right".

For Task 5 (placing an item between grid cells), use any item in section 1 or 4. Every Canvas starts in Grid mode, so participants have to find Freeform (block Settings > Layout, or the right-click menu) themselves.

## Files

- `blueprint.json` - the Playground blueprint
- `setup.php` - seeds the site (options, style variation, media, pages, front page)
- `content/*.html` - block markup for each page, with `%%NAME_ID%%` / `%%NAME_URL%%` / `%%NAME_ALT%%` placeholders for the imported images
- `canvas.zip` - the pinned Canvas plugin build

## Updating Canvas

The zip is committed rather than pulled from GitHub Actions, for two reasons. Canvas's build artifacts expire after 90 days, and pinning means every participant sees the same version.

To move to a newer build, download the `canvas` artifact from a successful [Build Playground run](https://github.com/Automattic/canvas/actions/workflows/playground.yml), extract `canvas.zip`, replace the one here and update the commit reference above.

## Editing content

Edit the files in `content/` and push to `trunk`. Raw GitHub files are cached for about five minutes, so a change can take that long to reach Playground.
