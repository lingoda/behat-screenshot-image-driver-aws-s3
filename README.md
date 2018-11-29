S3 image driver for Behat-ScreenshotExtension
=========================
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/lingoda/behat-screenshot-image-driver-aws-s3/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/lingoda/behat-screenshot-image-driver-aws-s3/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/lingoda/behat-screenshot-image-driver-aws-s3/badges/build.png?b=master)](https://scrutinizer-ci.com/g/lingoda/behat-screenshot-image-driver-aws-s3/build-status/master)
[![Build Status](https://travis-ci.org/lingoda/behat-screenshot-image-driver-aws-s3.svg?branch=master)](https://travis-ci.org/lingoda/behat-screenshot-image-driver-aws-s3)

This package is an image driver for the [bex/behat-screenshot](https://github.com/elvetemedve/behat-screenshot) behat extension which uploads to S3.

Installation
------------

Install by adding to your `composer.json`:

```bash
composer require --dev lingoda/behat-screenshot-image-driver-aws-s3
```

Configuration
-------------

Enable the image driver in the Behat-ScreenshotExtension's config in `behat.yml` like this:

```yml
default:
  extensions:
    Bex\Behat\ScreenshotExtension:
      active_image_drivers: aws_s3
      image_drivers:
        aws_s3:
          bucket: BUCKET # Required
          region: REGION # Required
          version: latest # Default
          credentials_key: AWS_S3_KEY # Optional
          credentials_secret: AWS_S3_SECRET # Optional
          credentials_token: AWS_S3_TOKEN # Optional
          timeout: 30 # Optional, number of minutes the screenshot will be available when private
```

Usage
-----

When you run behat and a step fails then the Behat-ScreenshotExtension will automatically take the screenshot and will pass it to the image driver, which will return the S3 image url. So you will see something like this:

```bash
  Scenario:                           # features/feature.feature:2
    Given I have a step               # FeatureContext::passingStep()
    When I have a failing step        # FeatureContext::failingStep()
      Error (Exception)
Screenshot has been taken. Open image at http://us-west-1.aws.com/....
    Then I should have a skipped step # FeatureContext::skippedStep()
```
