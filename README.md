# Asset manager module. 

## Designed for small business.

Use this module to manage all your risks. Define organisational units and risk owners. Specify Objectives for these units. Specify Risks to these Objectives being achieved.
Specify Controls to mitigate risks. Assign Actions and Indicators to Objectives/Risks/Controls so that they can be monitored. Link Owners to all of the above so that they can be Informed, held accountable or made responsible to capture feedback.

## Requires Seriti Slim 3 MySQL Framework skeleton

This module integrates seamlessly into [Seriti skeleton framework](https://github.com/seriti/slim3-skeleton).
You need to first install the skeleton framework and then download the source files for the module and follow these instructions.

It is possible to use this module independantly from the seriti skeleton but you will still need the [Seriti tools library](https://github.com/seriti/tools).
It is strongly recommended that you first install the seriti skeleton to see a working example of code use before using it within another application framework.
That said, if you are an experienced PHP programmer you will have no problem doing this and the required code footprint is very small.  

## Install the module

1.) Install Seriti Skeleton framework(see the framework readme for detailed instructions) : 
    **composer create-project seriti/slim3-skeleton [directory-for-app]**. 
    Make sure that you have this working before you proceed.

2.) Download a copy of Asset manager module source code directly from github and unzip,
or by using **git clone https://github.com/seriti/risk-manager** from command line.
Once you have a local copy of module code check that it has following structure:

/Risk/(all module implementation classes are in this folder)  
/setup_app.php  
/routes.php  

3.) Copy the **Risk** folder and all its contents into **[directory-for-app]/app** folder.

4.) Open the routes.php file and insert the <code>$this->group('/risk', function (){}</code> route definition block
within the existing  <code>$app->group('/admin', function () {}</code> code block contained in existing skeleton **[directory-for-app]/src/routes.php** file.

5.) Open the setup_app.php file and  add the module config code snippet into bottom of skeleton **[directory-for-app]/src/setup_app.php** file.
Please check the "table_prefix" value to ensure that there will not be a clash with any existing tables in your database.

6.) Now in your browser goto URL:  
 
**"http://localhost:8000/admin/risk/dashboard"** if you are using php built in server  
OR  
**"http://www.yourdomain.com/admin/risk/dashboard"** if you have configured a domain on your server  
OR
Click **Dashboard** menu option and you will see list of available modules, click **Risk manager**  

Now click link at bottom of page **Setup Database**: This will create all necessary database tables with table_prefix as defined above.
Thats it, you are good to go. Goto Tasks tab, setup organisational units and risk owners. Then use Objectives tab to define objectives for units;
Risks tab to define risks to achieving these objectives; Controls tab to define risk controls; Assign Actions & Indicators; Link all to owners.
**Still to come is owner interface to process owner responses and indicator values.**