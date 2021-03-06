<?php

/**
 * @file
 * Contains \Drupal\Tests\big_pipe\Unit\Render\Placeholder\BigPipeStrategyTest.
 */

namespace Drupal\Tests\big_pipe\Unit\Render\Placeholder;

use Drupal\big_pipe\Render\Placeholder\BigPipeStrategy;
use Drupal\Core\Session\SessionConfigurationInterface;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @coversDefaultClass \Drupal\big_pipe\Render\Placeholder\BigPipeStrategy
 * @group big_pipe
 */
class BigPipeStrategyTest extends UnitTestCase {

  /**
   * @covers ::processPlaceholders
   *
   * @dataProvider placeholdersProvider
   */
  public function testProcessPlaceholders(array $placeholders, $request_has_session, $request_has_big_pipe_nojs_cookie, array $expected_big_pipe_placeholders) {
    $request = new Request();
    if ($request_has_big_pipe_nojs_cookie) {
      $request->cookies->set(BigPipeStrategy::NOJS_COOKIE, 1);
    }
    $request_stack = $this->prophesize(RequestStack::class);
    $request_stack->getCurrentRequest()
      ->willReturn($request)
      ->shouldBeCalled();

    $session_configuration = $this->prophesize(SessionConfigurationInterface::class);
    $session_configuration->hasSession(Argument::type(Request::class))
      ->willReturn($request_has_session)
      ->shouldBeCalled();

    $big_pipe_strategy = new BigPipeStrategy($session_configuration->reveal(), $request_stack->reveal());
    $processed_placeholders = $big_pipe_strategy->processPlaceholders($placeholders);

    if ($request_has_session) {
      $this->assertSameSize($expected_big_pipe_placeholders, $processed_placeholders, 'BigPipe is able to deliver all placeholders.');
      foreach (array_keys($placeholders) as $placeholder) {
        $this->assertSame($expected_big_pipe_placeholders[$placeholder], $processed_placeholders[$placeholder], "Verifying how BigPipeStrategy handles the placeholder '$placeholder'");
      }
    }
    else {
      $this->assertSame(0, count($processed_placeholders));
    }
  }

  /**
   * @see \Drupal\big_pipe\Tests\BigPipePlaceholderTestCases
   */
  public function placeholdersProvider() {
    $cases = \Drupal\big_pipe\Tests\BigPipePlaceholderTestCases::cases();

    // Generate $placeholders variable as expected by
    // \Drupal\Core\Render\Placeholder\PlaceholderStrategyInterface::processPlaceholders().
    $placeholders = [
      $cases['html']->placeholder                             => $cases['html']->placeholderRenderArray,
      $cases['html_attribute_value']->placeholder             => $cases['html_attribute_value']->placeholderRenderArray,
      $cases['html_attribute_value_subset']->placeholder      => $cases['html_attribute_value_subset']->placeholderRenderArray,
      $cases['edge_case__invalid_html']->placeholder          => $cases['edge_case__invalid_html']->placeholderRenderArray,
      $cases['edge_case__html_non_lazy_builder']->placeholder => $cases['edge_case__html_non_lazy_builder']->placeholderRenderArray,
    ];

    return [
      'no session, no-JS cookie absent' => [$placeholders, FALSE, FALSE, []],
      'no session, no-JS cookie present' => [$placeholders, FALSE, TRUE, []],
      'session, no-JS cookie absent: (JS-powered) BigPipe placeholder used for HTML placeholders' => [$placeholders, TRUE, FALSE, [
        $cases['html']->placeholder                             => $cases['html']->bigPipePlaceholderRenderArray,
        $cases['html_attribute_value']->placeholder             => $cases['html_attribute_value']->bigPipeNoJsPlaceholderRenderArray,
        $cases['html_attribute_value_subset']->placeholder      => $cases['html_attribute_value_subset']->bigPipeNoJsPlaceholderRenderArray,
        $cases['edge_case__invalid_html']->placeholder          => $cases['edge_case__invalid_html']->bigPipeNoJsPlaceholderRenderArray,
        $cases['edge_case__html_non_lazy_builder']->placeholder => $cases['edge_case__html_non_lazy_builder']->bigPipePlaceholderRenderArray,
      ]],
      'session, no-JS cookie present: no-JS BigPipe placeholder used for HTML placeholders' => [$placeholders, TRUE, TRUE, [
        $cases['html']->placeholder                             => $cases['html']->bigPipeNoJsPlaceholderRenderArray,
        $cases['html_attribute_value']->placeholder             => $cases['html_attribute_value']->bigPipeNoJsPlaceholderRenderArray,
        $cases['html_attribute_value_subset']->placeholder      => $cases['html_attribute_value_subset']->bigPipeNoJsPlaceholderRenderArray,
        $cases['edge_case__invalid_html']->placeholder          => $cases['edge_case__invalid_html']->bigPipeNoJsPlaceholderRenderArray,
        $cases['edge_case__html_non_lazy_builder']->placeholder => $cases['edge_case__html_non_lazy_builder']->bigPipeNoJsPlaceholderRenderArray,
      ]],
    ];
  }

}
