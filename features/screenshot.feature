Feature: Taking screenshot
  In order to debug failing scenarios more easily
  As a developer
  I should see a screenshot of the browser window of the failing step

  Background:
    Given I have the file "index.html" in document root:
      """
      <!DOCTYPE html>
      <html>
          <head>
              <meta charset="UTF-8">
              <title>Test page</title>
              <style>
                  body {background-color: #a9a9a9;}
              </style>
          </head>

          <body>
              <h1>Lorem ipsum dolor amet.</h1>
          </body>
      </html>
      """
    And I have a web server running on host "localhost" and port "8080"
    And I have the feature:
      """
      Feature: Multi-step feature
      Scenario:
        Given I have a step
        When I have a failing step
        Then I should have a skipped step
      """
    And I have the context:
      """
      <?php
      use Behat\MinkExtension\Context\RawMinkContext;
      class FeatureContext extends RawMinkContext
      {
          /**
           * @Given I have a step
           */
          function passingStep()
          {
            $this->visitPath('index.html');
          }
          /**
           * @When I have a failing step
           */
          function failingStep()
          {
            throw new Exception('Error');
          }
          /**
           * @Then I should have a skipped step
           */
          function skippedStep()
          {}
      }
      """

  Scenario: Save screenshot using AWS S3 api
    Given I have the configuration:
      """
      default:
        extensions:
          Behat\MinkExtension:
            base_url: 'http://localhost:8080'
            sessions:
              default:
                selenium2:
                  wd_host: http://localhost:4444/wd/hub
                  browser: phantomjs

          Bex\Behat\ScreenshotExtension:
            active_image_drivers: aws_s3
            image_drivers:
              aws_s3:
                bucket: test_screenshot
                region: eu-west-1
                credentials_key: asd
                credentials_secret: bus
                client_factory: Bex\Behat\ScreenshotExtension\Driver\Tests\MockClientFactory::getClient
      """
    When I run Behat
    Then I should see a failing test
    And I should see the image url
