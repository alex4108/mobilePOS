<?php
// DEPLOY TEST 9000
/** 
 * Git Deployment Script for BitBucket
 *
 * All the existing PHP Git deploy scripts seem to rely on the repository copy on the web server, 
 * and the web site files themselves, all being writable by the web server user (e.g. apache or www-data). 
 * From a security point of view this is far from ideal.
 * 
 * This script does pretty much the same as the others, except it can also be called via cron. 
 * Symlink it in to web space and create your URL for a BitBucket Hook POST Request. Hits to this URL from 
 * BitBucket will cause an empty file to be written.
 *
 * Then, cron the script to run every minute or whatever. When run from cron, it looks for the above file. 
 * If it finds it, it does the Git checkout under the permissions of the system user account and NOT the 
 * web server user. Once this is done it deletes the data file.
 *
 * @Author: Ben Roberts ben@headsnet.com
 *
 */

// Include the class
include('class.bitbucket-cron-deploy.php');

// Where you have put your DETACHED HEAD repository
$repo_path = '/home/alex/mobilePOS/mobilepos.git';

// Where you want to deploy the files to
$root_path = '/home/alex/public_html';

// The name of the branch to deploy.
$git_branch = 'master';

// If you need to specify the full path to the git binary (if it's not in your PATH)
$git_path = '/usr/bin/git';




// Instantiate a new instance
$myDeploy = new cronDeploy();

// Set config vars
$myDeploy->setRepoPath($repo_path); 
$myDeploy->setRootPath($root_path); 
$myDeploy->setGitBranch($git_branch); 
$myDeploy->setGitPath($git_path);

// Run the deployment
$myDeploy->deploy();


