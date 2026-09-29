# Canvas block - interaction and usability

Playground environment for the moderated Canvas block sessions. It tests whether people can reorganise and build layouts with Canvas without help.

## Open the test site

[Launch in WordPress Playground](https://playground.wordpress.net/?blueprint-url=https%3A%2F%2Fraw.githubusercontent.com%2Fgetdave%2Fwordpress-user-testing%2Ftrunk%2Fcanvas-block%2Fblueprint.json)

It opens straight into the block editor on the homepage, logged in as admin. Every visit starts from a clean site, so reload the link to reset between participants.

## What's in the site

- WordPress 7.1.2 on PHP 8.3, both pinned in `blueprint.json` (Canvas needs WordPress 7.1 or newer)
- Twenty Twenty-Five with the Morning style variation
- Canvas, pinned to a build of a single [Automattic/canvas](https://github.com/Automattic/canvas) commit. `CANVAS_VERSION` records which one.
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
- `CANVAS_VERSION` - the Canvas commit, build date and checksum for `canvas.zip`
- `update-canvas.sh` - rebuilds `canvas.zip` from a chosen Canvas commit

## Updating Canvas

Canvas has no formal releases yet and its `trunk` changes often. So the blueprint never installs Canvas straight from GitHub. It installs the `canvas.zip` committed here, which is built from one known commit. New commits to Canvas can't change the test site until someone deliberately updates this zip.

### Check the current version

```sh
cat CANVAS_VERSION
```

### Update to a new build

You need Git, Node.js 22 or newer and npm.

```sh
./update-canvas.sh              # latest commit on Canvas trunk (the default)
./update-canvas.sh latest       # same thing
./update-canvas.sh 7141416      # a specific commit (short or full SHA)
./update-canvas.sh some-branch  # a branch or tag
```

The script clones Canvas, checks out the commit and builds the plugin with Canvas's own `npm run package:plugin`. It then replaces `canvas.zip` and rewrites `CANVAS_VERSION`. It doesn't commit anything.

After it runs:

1. Open the blueprint in Playground and run through the tasks. Canvas's saved format is still experimental, so check that the seeded sections in `content/home.html` still load without block errors.
2. Commit `canvas.zip` and `CANVAS_VERSION` together, with the Canvas commit in the message.
3. Don't update Canvas partway through a round of sessions. Every participant in a round should see the same build.

### Updating WordPress

WordPress is pinned with `preferredVersions.wp` in `blueprint.json`. Change it deliberately between rounds, not during one.

## Editing content

Edit the files in `content/` and push to `trunk`. Raw GitHub files are cached for about five minutes, so a change can take that long to reach Playground.
