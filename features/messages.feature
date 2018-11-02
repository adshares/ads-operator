Feature: Messages
  In order to display messages in blockexplorer
  As an API client
  I need to be able to fetch single message and list of messages

  Background:
    Given "messages" exist in application:
      | message_id    | node | blockId  | hash                                                             | length | transactionCount |
      | 0001:00000001 | 0001 | 1B6180E0 | 70D11F677A9B8F0A49BAD9DCE9F715FCE4AA76BDF23C14FB40142999A1E84577 | 0001   | 1                |
      | 0002:00000002 | 0002 | 2B6180E0 | 70D11F677A9B8F0A49BAD9DCE9F715FCE4AA76BDF23C14FB40142999A1E84577 | 0002   | 2                |
      | 0003:00000003 | 0003 | 3B6180E0 | A534B0451771A35021BF47E09F0865C3E652B78B8104CDF9944A5709870BF63B | 0003   | 3                |
      | 0004:00000004 | 0004 | 4B6180E0 | 70B8290E9DB0DC611CAAA3E38DF11B7D0E1EC41219AB00CF7B7B628644460429 | 0004   | 4                |
      | 0005:00000005 | 0005 | 5B6180E0 | 07187918DEC935E75D00B967B8AC8FF350168ED27A9A6ADFE78A7141B60F0313 | 0005   | 5                |
      | 0001:00000006 | 0001 | 6B6180E0 | 26710F00488043124564798C1D5B617CE54371C6334D54987FF0991A25A5324D | 0006   | 1                |
      | 0001:00000007 | 0001 | 7B6180E0 | BFACA42C051F87BD312D1DDF044D5C18DAAEDF47563214D3C107E688FD5BF29A | 0007   | 1                |
      | 0001:00000008 | 0001 | 8B6180E0 | 35657662CE38CDE131BD18F1538C1B1D8FC710A108FBFC9D5A00AB88EB9EB041 | 0008   | 1                |
      | 0001:00000009 | 0001 | 9B6180E0 | 5569B007386AB86D9B7760C5D6EF9E60DA1A1378FA2C602345D8E7C88B75129B | 0009   | 1                |
      | 0001:00000010 | 0001 | 1C6180E0 | 4F91E5E259BB89E012A28508EA180EA93A9E231857CC4E0CB2F2649BB11D3E3D | 0010   | 1                |
      | 0001:00000011 | 0001 | 1C6180E0 | 4F91E5E259BB89E012A28508EA180EA93A9E231857CC4E0CB2F2649BB11D3E3D | 0010   | 1                |
      | 0001:00000012 | 0002 | 1C6180E0 | 4F91E5E259BB89E012A28508EA180EA93A9E231857CC4E0CB2F2649BB11D3E3D | 0010   | 1                |
      | 0001:00000013 | 0002 | 1C6180E0 | 4F91E5E259BB89E012A28508EA180EA93A9E231857CC4E0CB2F2649BB11D3E3D | 0010   | 1                |
      | 0001:00000014 | 0003 | 1C6180E0 | 4F91E5E259BB89E012A28508EA180EA93A9E231857CC4E0CB2F2649BB11D3E3D | 0010   | 1                |
      | 0001:00000015 | 0003 | 1C6180E0 | 4F91E5E259BB89E012A28508EA180EA93A9E231857CC4E0CB2F2649BB11D3E3D | 0010   | 1                |

  Scenario: List all available messages without sort and pagination
    Given I want to get the list of "blockexplorer/messages"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
      [
        {
          "block_id":"5B6180E0",
          "hash":"07187918DEC935E75D00B967B8AC8FF350168ED27A9A6ADFE78A7141B60F0313",
          "length":5,
          "id":"0005:00000005",
          "transaction_count":5
        },
        {
          "block_id":"4B6180E0",
          "hash":"70B8290E9DB0DC611CAAA3E38DF11B7D0E1EC41219AB00CF7B7B628644460429",
          "length":4,
          "id":"0004:00000004",
          "transaction_count":4
        },
        {
          "block_id":"3B6180E0",
          "hash":"A534B0451771A35021BF47E09F0865C3E652B78B8104CDF9944A5709870BF63B",
          "length":3,
          "id":"0003:00000003",
          "transaction_count":3
        },
        {
          "block_id":"2B6180E0",
          "hash":"70D11F677A9B8F0A49BAD9DCE9F715FCE4AA76BDF23C14FB40142999A1E84577",
          "length":2,
          "id":"0002:00000002",
          "transaction_count":2
        },
        {
          "block_id":"1C6180E0",
          "hash":"4F91E5E259BB89E012A28508EA180EA93A9E231857CC4E0CB2F2649BB11D3E3D",
          "length":10,
          "id":"0001:00000015",
          "transaction_count":1
        },
        {
          "block_id":"1C6180E0",
          "hash":"4F91E5E259BB89E012A28508EA180EA93A9E231857CC4E0CB2F2649BB11D3E3D",
          "length":10,
          "id":"0001:00000014",
          "transaction_count":1
        },
        {
          "block_id":"1C6180E0",
          "hash":"4F91E5E259BB89E012A28508EA180EA93A9E231857CC4E0CB2F2649BB11D3E3D",
          "length":10,
          "id":"0001:00000013",
          "transaction_count":1
        },
        {
          "block_id":"1C6180E0",
          "hash":"4F91E5E259BB89E012A28508EA180EA93A9E231857CC4E0CB2F2649BB11D3E3D",
          "length":10,
          "id":"0001:00000012",
          "transaction_count":1
        },
        {
          "block_id":"1C6180E0",
          "hash":"4F91E5E259BB89E012A28508EA180EA93A9E231857CC4E0CB2F2649BB11D3E3D",
          "length":10,
          "id":"0001:00000011",
          "transaction_count":1
        },
        {
          "block_id":"1C6180E0",
          "hash":"4F91E5E259BB89E012A28508EA180EA93A9E231857CC4E0CB2F2649BB11D3E3D",
          "length":10,
          "id":"0001:00000010",
          "transaction_count":1
        },
        {
          "block_id":"9B6180E0",
          "hash":"5569B007386AB86D9B7760C5D6EF9E60DA1A1378FA2C602345D8E7C88B75129B",
          "length":9,
          "id":"0001:00000009",
          "transaction_count":1
        },
        {
          "block_id":"8B6180E0",
          "hash":"35657662CE38CDE131BD18F1538C1B1D8FC710A108FBFC9D5A00AB88EB9EB041",
          "length":8,
          "id":"0001:00000008",
          "transaction_count":1
        },
        {
          "block_id":"7B6180E0",
          "hash":"BFACA42C051F87BD312D1DDF044D5C18DAAEDF47563214D3C107E688FD5BF29A",
          "length":7,
          "id":"0001:00000007",
          "transaction_count":1
        },
        {
          "block_id":"6B6180E0",
          "hash":"26710F00488043124564798C1D5B617CE54371C6334D54987FF0991A25A5324D",
          "length":6,
          "id":"0001:00000006",
          "transaction_count":1
        },
        {
          "block_id":"1B6180E0",
          "hash":"70D11F677A9B8F0A49BAD9DCE9F715FCE4AA76BDF23C14FB40142999A1E84577",
          "length":1,
          "id":"0001:00000001",
          "transaction_count":1
        }
      ]
    """

  Scenario: List all available messages with sort by asc
    Given I want to get the list of "blockexplorer/messages"
    And I want to limit to 7
    And I want to order by "asc"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
        """
      [
         {
           "block_id":"1B6180E0",
           "hash":"70D11F677A9B8F0A49BAD9DCE9F715FCE4AA76BDF23C14FB40142999A1E84577",
           "length":1,
           "id":"0001:00000001",
           "transaction_count":1
         },
         {
           "block_id":"6B6180E0",
           "hash":"26710F00488043124564798C1D5B617CE54371C6334D54987FF0991A25A5324D",
           "length":6,
           "id":"0001:00000006",
           "transaction_count":1
         },
         {
           "block_id":"7B6180E0",
           "hash":"BFACA42C051F87BD312D1DDF044D5C18DAAEDF47563214D3C107E688FD5BF29A",
           "length":7,
           "id":"0001:00000007",
           "transaction_count":1
         },
         {
           "block_id":"8B6180E0",
           "hash":"35657662CE38CDE131BD18F1538C1B1D8FC710A108FBFC9D5A00AB88EB9EB041",
           "length":8,
           "id":"0001:00000008",
           "transaction_count":1
         },
         {
           "block_id":"9B6180E0",
           "hash":"5569B007386AB86D9B7760C5D6EF9E60DA1A1378FA2C602345D8E7C88B75129B",
           "length":9,
           "id":"0001:00000009",
           "transaction_count":1
         },
         {
           "block_id":"1C6180E0",
           "hash":"4F91E5E259BB89E012A28508EA180EA93A9E231857CC4E0CB2F2649BB11D3E3D",
           "length":10,
           "id":"0001:00000010",
           "transaction_count":1
         },
         {
           "block_id":"1C6180E0",
           "hash":"4F91E5E259BB89E012A28508EA180EA93A9E231857CC4E0CB2F2649BB11D3E3D",
           "length":10,
           "id":"0001:00000011",
           "transaction_count":1
         }
      ]
    """
  Scenario: List all available messages with sort by id
    Given I want to get the list of "blockexplorer/messages"
    And I want to limit to 2
    And I want to sort by "id"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
      [
        {
          "block_id":"5B6180E0",
          "hash":"07187918DEC935E75D00B967B8AC8FF350168ED27A9A6ADFE78A7141B60F0313",
          "length":5,
          "id":"0005:00000005",
          "transaction_count":5
        },
        {
          "block_id":"4B6180E0",
          "hash":"70B8290E9DB0DC611CAAA3E38DF11B7D0E1EC41219AB00CF7B7B628644460429",
          "length":4,
          "id":"0004:00000004",
          "transaction_count":4
        }
      ]
    """

  Scenario: List all available messages with sort by id
    Given I want to get the list of "blockexplorer/messages"
    And I want to limit to 2
    And I want to sort by "block_id"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
      [
        {
          "block_id":"9B6180E0",
          "hash":"5569B007386AB86D9B7760C5D6EF9E60DA1A1378FA2C602345D8E7C88B75129B",
          "length":9,
          "id":"0001:00000009",
          "transaction_count":1
        },
        {
          "block_id":"8B6180E0",
          "hash":"35657662CE38CDE131BD18F1538C1B1D8FC710A108FBFC9D5A00AB88EB9EB041",
          "length":8,
          "id":"0001:00000008",
          "transaction_count":1
        }
      ]
    """

  Scenario: List all available messages with sort by asc
    Given I want to get the list of "blockexplorer/messages"
    And I want to limit to 2
    And I want to order by "asc"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
      [
        {
          "block_id":"1B6180E0",
          "hash":"70D11F677A9B8F0A49BAD9DCE9F715FCE4AA76BDF23C14FB40142999A1E84577",
          "length":1,
          "id":"0001:00000001",
          "transaction_count":1
        },
        {
          "block_id":"6B6180E0",
          "hash":"26710F00488043124564798C1D5B617CE54371C6334D54987FF0991A25A5324D",
          "length":6,
          "id":"0001:00000006",
          "transaction_count":1
        }
      ]
    """

  Scenario: List all available messages with offset and sort by id asc
    Given I want to get the list of "blockexplorer/messages"
    And I want to offset to 14
    And I want to sort by "id"
    And I want to order by "asc"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
      [
        {
          "block_id":"5B6180E0",
          "hash":"07187918DEC935E75D00B967B8AC8FF350168ED27A9A6ADFE78A7141B60F0313",
          "length":5,
          "id":"0005:00000005",
          "transaction_count":5
        }
      ]
    """

  Scenario: List all available messages with limit, offset and sort by id asc
    Given I want to get the list of "blockexplorer/messages"
    And I want to limit to 2
    And I want to offset to 2
    And I want to sort by "id"
    And I want to order by "asc"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
      [
        {
          "block_id":"7B6180E0",
          "hash":"BFACA42C051F87BD312D1DDF044D5C18DAAEDF47563214D3C107E688FD5BF29A",
          "length":7,
          "id":"0001:00000007",
          "transaction_count":1
        },
        {
          "block_id":"8B6180E0",
          "hash":"35657662CE38CDE131BD18F1538C1B1D8FC710A108FBFC9D5A00AB88EB9EB041",
          "length":8,
          "id":"0001:00000008",
          "transaction_count":1
        }
      ]
    """

  Scenario: Unable to get list of messages with invalid sort field
    Given I want to get the list of "blockexplorer/messages"
    And I want to sort by "test"
    When I request resource
    Then the response status code should be 400
    And the response should contain:
    """
        {
          "code":400,
          "message":"Sort value `test` is invalid. Only id, blockId values are supported."
        }
    """

  Scenario: Unable to get list of messages with invalid order field
    Given I want to get the list of "blockexplorer/messages"
    And I want to order by "test"
    When I request resource
    Then the response status code should be 400
    And the response should contain:
    """
        {
          "code": 400,
          "message": "Order value `test` is invalid. Only `desc` and `asc` values are supported."
        }
    """

  Scenario: Unable to get list of messages with invalid limit field
    Given I want to get the list of "blockexplorer/messages"
    And I want to limit to "-10"
    When I request resource
    Then the response status code should be 400
    And the response should contain:
    """
        {
          "code": 400,
          "message": "Limit value `-10` is invalid. Value must be between 1 and 100."
        }
    """

  Scenario: Unable to get list of messages with invalid offset field
    Given I want to get the list of "blockexplorer/messages"
    And I want to offset to "-10"
    When I request resource
    Then the response status code should be 400
    And the response should contain:
    """
        {
          "code": 400,
          "message": "Offset value `-10` is invalid. Value must be between 0 and 9223372036854775807."
        }
    """

  Scenario: Get single messages
    Given I want to get the resource "blockexplorer/messages" with id "0001:00000001"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
        {
          "block_id":"1B6180E0",
          "hash":"70D11F677A9B8F0A49BAD9DCE9F715FCE4AA76BDF23C14FB40142999A1E84577",
          "length":1,
          "id":"0001:00000001",
          "transaction_count":1
        }
    """

  Scenario: Unable to get non-existent resource
    Given I want to get the resource "blockexplorer/messages" with id "0011:00000001"
    When I request resource
    Then the response status code should be 404
    And the response should contain:
    """
        {
          "code": 404,
          "message": "The requested resource: 0011:00000001 was not found"
        }
    """

  Scenario: Unable to get the resource by invalid id
    Given I want to get the resource "blockexplorer/messages" with id "123-22"
    When I request resource
    Then the response status code should be 422
    And the response should contain:
    """
        {
          "code": 422,
          "message": "Invalid resource identity"
        }
    """

  Scenario: Unable to get the resource by invalid id
    Given I want to get the resource "blockexplorer/messages" with id "0001*"
    When I request resource
    Then the response status code should be 422
    And the response should contain:
    """
        {
          "code":422,
          "message":"Invalid resource identity"
        }
    """