<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    /** @var TestRunnerContext $testRunnerContext */
    private $testRunnerContext;

    /**
     * @BeforeScenario
     */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->testRunnerContext = $environment->getContext('Bex\Behat\Context\TestRunnerContext');
    }

    /**
     * @Then I should see the image url
     */
    public function iShouldSeeTheDummyImageUrl()
    {
        $message = 'https://s3-eu-west-1.amazonaws.com/test_screenshot/1234/features_feature_feature_2.png';

        $output = $this->testRunnerContext->getStandardOutputMessage() .
            $this->testRunnerContext->getStandardErrorMessage();
        $this->assertOutputContainsMessage($output, $message);
    }

    /**
     * @param $message
     */
    private function assertOutputContainsMessage($output, $message)
    {
        if (mb_strpos($output, $message) === false) {
            throw new RuntimeException('Behat output did not contain the given message. Output: ' . PHP_EOL . $output);
        }
    }
}
