KXM Yii implementation details
===============================

Overview
---------
The KXM implementation leverages improvements to the base Yii v1.1x web application functionality.  At the heart of the improvements are three key concepts:
 1. Modifying the vanilla Yii sidebar navigation is time consuming, routine, and may be abstracted
 1. Customizations to gii-generated scaffolds are routine and predictable
 1. The vanilla Yii install lacks some convenience methods and features developed (and routinely redeveloped) over time

The customizations may be broken into two rough categories:

 1. Code generators
 1. Components

### Conventions
The following conventions are used in this document:
 - [app directory]  
   The application directory root.  index.php resides in this directory
 - [code directory]  
   The code directory.  In a vanilla Yii installation, the [code directory] is named `protected`, though the name may be changed.  See below for details

Details
--------

### Code generators
These are custom php scripts that are leveraged by gii to build models, controllers, and views that leverage the custom components (plus a few ancilary code tweaks and comment cleanup).  The directory structure under gii should match a vanilla Yii installation, so the templates may be copied directly over the `yii\framework\gii directory`, resulting in the paths `yii\framework\gii\generators\model\templates\KXM` and `yii\framework\gii\generators\crud\templates\KXM`.  The custom templates should be copied before launching gii, and they will appear as a dropdown item labeled 'KXM' under the 'Code Template' select box.

### Components
Custom derived CActiveRecord, CController, and CMenu classes, as well as a custom Announcer and Announcement classes leveraged by both KXMMenu and the gii template classes.  These custom classes must be installed before invoking gii, as KXMCActiveRecord and KXMController are both referenced during scaffolding.
To install, copy the complete contents of 'components' to the application 'components' directory

### Important notes
To derive the benefits
 - the base class for model generation must be KXMCActiveRecord
 - The base class for controller generation (under crud generation) must be KXMController

### Installation
 1. If this is a new Yii framework installation, migrate the KXM generators into the proper locations, usually
    1. yii/framework/gii/generators/model/templates
    1. yii/framework/gii/generators/crud/templates
 1. Generate yii webapp as directed:  
    `yiic webapp [app directory]`
 1. By convention, I change the name of the standard code directory from `protected` to `_application`.  If you do so, then change the configuration reference in `[code directory]/index.php` to reflect the new location.  
    `$config = dirname(__FILE__).'/[code directory name]/config/main.php';`  
    For this example:  
    `$config = dirname(__FILE__).'/_application/config/main.php';`
 1. Copy KXMApplication to [code directory] root
 1. Update `[app directory]/index.php` to reflect new KXMApplication class (A modified `index.php` file is included in the repo for your convenience):  
    `$config = dirname(__FILE__).'/_application/config/main.php';`  
    `$app    = dirname(__FILE__).'/_application/KXMApplication.php';`  
    ...  
    `require_once($yii);`  
    `require_once($app);`  
    `Yii::createApplication('KXMApplication', $config)->run();`
 1. Copy KXM components into [code directory]/components
 1. Use the Yii scaffolding tool, gii, to generate models and crud specifying the KXM templates
 1. Update `views/layouts/main.php` to reference the KXMMenu for the sidebar.  The included announcer will prompt the appropriate controller when it is time to make context navigation
