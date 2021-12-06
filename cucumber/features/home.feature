Feature: View home feature
  In order to check some home page content
  As a guest user
  I want to be able to view home page

  @smoke
  Scenario: View home page content
    Given I am guest user
    When I open "/" page
    Then I see welcome block