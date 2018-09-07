Feature: Auth
  In order to make any transactions on the ADS Operator Panel
  As an API client
  I need to be able to register a new account

  Background:
    Given users already exist in application:
      | email              | password  |
      | user1@adshares.net | password1 |
      | user2@adshares.net | password2 |

  Scenario: Register a new account when email is invalid
    Given I want to create a user
    When I provide the data:
    """
      {
        "email": "user.pl",
        "password": "password"
      }
    """
    When I request resource
    Then the response status code should be 400
    And the response should contain:
    """
      {
        "errors":
        {
          "email":
          [
            "This value is not a valid email address."
          ]
        },
        "message":"Validation failed",
        "code":400
       }
    """

  Scenario: Register a new account when user already exists
    Given I want to create a user
    When I provide the data:
    """
      {
        "email": "user1@adshares.net",
        "password": "password"
      }
    """
    When I request resource
    Then the response status code should be 400
    And the response should contain:
    """
      {
        "errors":
        {
          "email":
          [
            "This value is already used."
          ]
        },
        "message":"Validation failed",
        "code":400
       }
    """

  Scenario: Register a new account when user does not exist
    Given I want to create a user
    When I provide the data:
    """
      {
        "email": "user_new@adshares.net",
        "password": "password"
      }
    """
    When I request resource
    Then the response status code should be 201
    And the response should contain:
    """
    """
