# laravel-template-front


## Quick Start

Install Node.js and Git and then:

```sh
$ git clone git://github.com/therealcreators/laravel_template
$ cd laravel_template
$ npm install -g gulp bower
$ npm install
$ gulp run
```

Visit `localhost:8000` in your browser.

There are a few commands to use:
To compile a local version
```sh
$ gulp dev
```
To compile a production version
```sh
$ gulp prod
```
Tu run a local server
```sh
$ gulp server
```
Tu run a file watcher
```sh
$ gulp server
```
Tu run a local server and a file watcher
```sh
$ gulp run
```
Enjoy!

## Components

The following have already been added because I believe they are the bare
minimum components you may need. Add or remove according to your requirements.

* [Angular JS](http://angularjs.org)
* [UI Router](https://github.com/angular-ui/ui-router)
* [Bootstrap](http://getbootstrap.com/)
* [UI Bootstrap](http://angular-ui.github.io/bootstrap)
* [Font Awesome](http://fontawesome.io)

## Learn

### Overall Directory Structure

At a high level, the structure looks roughly like this:

```
ng-launchpad/
  |- src/
  |  |- common/
  |  |  |- <shared modules>
  |  |- img/
  |  |  |- <images>
  |  |- less/
  |  |  |- main.less
  |  |  |- variables.less
  |  |- modules/
  |  |  |- <app modules>
  |  |- index.html
  |- vendor/
  |- .bowerrc
  |- bower.json
  |- build.config.js
  |- Gulpfile.js
  |- package.json
```

### File naming conventions for `src/common` and `src/modules`

* Start file names within a specific module with a common prefix.
  Example: In the `src/modules/home/` all file names should begin with `home`;
  `homeCtrl.js`, `home.tpl.html`, `home.less`.
* Templates: `*.tpl.html`.
  Templates in `src/common` are added to Angular's template cache and
  bundled into `templates-common` module in file `templates-common.js`.
  Templates in `src/modules` are added to Angular's template cache and
  bundled into `templates-modules` module in file `templates-modules.js`.
  Template names are without `src/common` or `src/modules` prefix.
