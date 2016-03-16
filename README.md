# dn-challenge

Problem to solve: Given a URL identify whether a technology is present on this page or website

You need to identify two technologies: Google Analytics and Dyn DNS

Here are their signatures:

    $ga_signatures = array( '.google-analytics.com/ga.js', 'ga.async = true;' ); // HTML lookup
    $dyn_signatures = array( 'dynect.net', 'dns.dyn.com' ); // DNS lookup


We will use a command line to run the script, passing the webpage to check as a command line argument.


Here are some examples that we will run your script against, so be sure to try these out and verify that they
work as expected:

    $ php checker.php google.com
    $ php checker.php http://www.google.com
    $ php checker.php http://facebook.com
    $ php checker.php http://www.datanyze.com
    $ php checker.php www.datanyze.com
    $ php checker.php datanyze.com

For each input, your output should be:

    Using GA: <yes/no>
    Using Dyn: <yes/no>


* No need to explicitly write tests, but please test your script on several sites on your own to make sure it works dependably and as expected.
* Design your code to be robust and scalable.  New signatures for any technology may be added in the future
* Comments are encouraged where you think necessary to explain your code and thought process
