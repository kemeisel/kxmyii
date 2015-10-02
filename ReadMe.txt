ReadMe.txt
Prepared on 2015-05-20 by Keith Meisel <kemeisel@gmail.com>
======================================
Overview
---------
The files in this archive represent commonly implemented customizations for Yii v1.1x web applications.  The customizations may be broken into two rough categories:

1) Components
2) Code generators


Components
-----------
Custom derived CActiveRecord, CController, and CMenu classes, as well as a custom Announcer and Announcement classes leveraged by both KXMMenu and the gii template classes described below.  These classes must be installed before invoking gii, as KXMCActiveRecord and KXMController are both referenced during scaffolding.
To install, copy the complete contents of 'components' to the application 'components' directory


Code generators
----------------
These are custom php scripts that build the models, controllers, and views that leverage the components (though there is also code improvements and cleanup present).  The directory structure under gii should match a vanilla Yii installation, so the templates may be copied directly over the yii\framework\gii directory, resulting in the paths 'yii\framework\gii\generators\[model|crud]\templates\KXM'.  The copying should take place before launching gii, and the custom templates will appear as a dropdown item labeled 'KXM' under the 'Code Template' select box.

Important notes:
* The base class for model generation must be KXMCActiveRecord
* The base class for controller generation (under crud generation) must be KXMController
