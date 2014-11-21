#!/bin/bash

# From the ExpressionEngine 2.9.2 User Guide:
#
# Set these files to 666:
#     
#     system/expressionengine/config/config.php
#     system/expressionengine/config/database.php
#
# Set these directories to 777:
#     
#     system/expressionengine/cache/
#     images/avatars/uploads/
#     images/captchas/
#     images/member_photos/
#     images/pm_attachments/
#     images/signature_attachments/
#     images/uploads/

chmod 666 $1system/expressionengine/config/{config,database}.php
chmod -R 777 $1system/expressionengine/cache
chmod -R 777 $1images/{avatars/uploads,captchas,member_photos,pm_attachments,signature_attachments,uploads}
chmod -R 777 $1themes/site_themes/paintvoice/img/{gallery,news,headers}