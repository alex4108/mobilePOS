<?php

// PLEASE NOTE: Anything changed in this file will NOT be pushed to the servers via automatic deployment, for deployment location determination issues
// If something needs to be changed, please modify config.php directly on the instance

// Where you have put your DETACHED HEAD repository
$repo_path = '/home/alex/mobilePOS_dev/mobilepos.git';

// Where you want to deploy the files to
$root_path = '/home/alex/public_html/dev.it.www.vm';

// The name of the branch to deploy.
$git_branch = 'develop';

// If you need to specify the full path to the git binary (if it's not in your PATH)
$git_path = '/usr/bin/git';

?>