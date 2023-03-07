# EasyTemplate

EasyTemplate is a PHP Template Parser that consequently seperates logic from the view. It simply replaces marked areas of an HTML file with content generated by a controller script. 

## Requirements

PHP 8.1.2

## Installation

````
$ composer require dahas/easy-template 
````

## Example

Templates are either complete HTML files or HTML segments that contain markers and slices.

````php
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>[[TITLE]]</title>
    </head>
    <body>
        <h1>[[HEADER]]</h1>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">NAME</th>
                    <th scope="col">AGE</th>
                    <th scope="col">CITY</th>
                </tr>
            </thead>
            <tbody>
                <!-- {{ROWS}} begin -->
                <tr>
                    <th scope="row">[[UID]]</th>
                    <td>[[NAME]]</td>
                    <td>[[AGE]]</td>
                    <td>[[CITY]]</td>
                </tr>
                <!-- {{ROWS}} end -->
            </tbody>
        </table>
    </body>
</html>
````

Here is how you load a template:

````php
$template = new EasyTemplate("path/to/template.html");
````

### Markers

Markers are placeholders. They'll be replaced with content when the HTML template gets parsed. A marker is surrounded by double square brackets, like so: `[[MARKER]]`

### Slices

A slice is an HTML segment within a template. It is defined by two markers, one at the beginning and one at the end of the segment. Both slice-markers are wrapped twice in curly braces. You can additionally wrap them in comments:

````php
<!-- {{ROWS}} begin -->
<tr>
    <th scope="row">[[UID]]</th>
    <td>[[NAME]]</td>
    <td>[[AGE]]</td>
    <td>[[CITY]]</td>
    <td>[[COUNTRY]]</td>
</tr>
<!-- {{ROWS}} end -->
````

Slices are treated like Templates. You can even put a Slice in its own HTML file. 

Getting the Slice within a Template:

````php
$slice = $this->template->getSlice("{{ROWS}}");
````

Loading the Slice from its own file:
````php
$slice = new EasyTemplate("path/to/slice.html");
````

### Parsing Markers and Slices

To replace Markers and Slices and return a valid HTML document you use the `parse()` method. This function takes an Array of Markers as the first and an Array of Slices as the second argument.

````php
$template = new EasyTemplate("path/to/template.html");

$markers = [
    "[[TITLE]]" => "The title",
    "[[HEADER]]" => "Easy Template is cool!"
];

$slice = $template->getSlice("{{ROWS}}");

$rows = $slice->parse([
    "[[UID]]" => 1,
    "[[NAME]]" => "Angus Young",
    "[[AGE]]" => 66,
    "[[CITY]]" => "Melbourne",
]);

$slices = [
    "{{ROWS}}" => $rows
];

return $template->parse($markers, $slices);
````