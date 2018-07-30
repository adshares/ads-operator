Feature: Nodes
  In order to display nodes in blockexplorer
  As an API client
  I need to be able to fetch single node and list of nodes

  Background:
    Given nodes exist in application:
      | id   | accountCount | balance | hash      | ipv4          | messageHash   | msid  | mtim                | port  | publicKey | status  |
      | 0001 | 1            | 1       | hash1     | 191.123.21.23 | messageHash1  | 1     | 2018-07-30 15:00:00 | 80    | publicKey | 1       |
      | 0002 | 2            | 2       | hash2     | 191.123.21.23 | messageHash2  | 2     | 2018-07-30 15:00:00 | 80    | publicKey | 2       |
      | 0003 | 3            | 3       | hash3     | 191.123.21.23 | messageHash3  | 3     | 2018-07-30 15:00:00 | 80    | publicKey | 3       |
      | 0004 | 4            | 4       | hash4     | 191.123.21.23 | messageHash4  | 4     | 2018-07-30 15:00:00 | 80    | publicKey | 4       |
      | 0005 | 5            | 5       | hash5     | 191.123.21.23 | messageHash5  | 5     | 2018-07-30 15:00:00 | 80    | publicKey | 5       |
      | 0006 | 6            | 6       | hash6     | 191.123.21.23 | messageHash6  | 6     | 2018-07-30 15:00:00 | 80    | publicKey | 6       |

  Scenario: List all available nodes without sort and pagination
    Given I want to get the list of "blockexplorer/nodes"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
      [
        {
          "account_count": 6,
          "balance": 600000000000,
          "hash": "hash6",
          "id": "0006",
          "ipv4": "191.123.21.23",
          "message_hash": "messageHash6",
          "msid": "6",
          "mtim": "2018-07-30T15:00:00+02:00",
          "port": 80,
          "public_key": "publicKey",
          "status": 6
        },
        {
          "account_count": 5,
          "balance": 500000000000,
          "hash": "hash5",
          "id": "0005",
          "ipv4": "191.123.21.23",
          "message_hash": "messageHash5",
          "msid": "5",
          "mtim": "2018-07-30T15:00:00+02:00",
          "port": 80,
          "public_key": "publicKey",
          "status": 5
        },
        {
          "account_count": 4,
          "balance": 400000000000,
          "hash": "hash4",
          "id": "0004",
          "ipv4": "191.123.21.23",
          "message_hash": "messageHash4",
          "msid": "4",
          "mtim": "2018-07-30T15:00:00+02:00",
          "port": 80,
          "public_key": "publicKey",
          "status": 4
        },
        {
          "account_count": 3,
          "balance": 300000000000,
          "hash": "hash3",
          "id": "0003",
          "ipv4": "191.123.21.23",
          "message_hash": "messageHash3",
          "msid": "3",
          "mtim": "2018-07-30T15:00:00+02:00",
          "port": 80,
          "public_key": "publicKey",
          "status": 3
        },
        {
          "account_count": 2,
          "balance": 200000000000,
          "hash": "hash2",
          "id": "0002",
          "ipv4": "191.123.21.23",
          "message_hash": "messageHash2",
          "msid": "2",
          "mtim": "2018-07-30T15:00:00+02:00",
          "port": 80,
          "public_key": "publicKey",
          "status": 2
        },
        {
          "account_count": 1,
          "balance": 100000000000,
          "hash": "hash1",
          "id": "0001",
          "ipv4": "191.123.21.23",
          "message_hash": "messageHash1",
          "msid": "1",
          "mtim": "2018-07-30T15:00:00+02:00",
          "port": 80,
          "public_key": "publicKey",
          "status": 1
        }
      ]
    """

  Scenario: List all available nodes with limit
    Given I want to get the list of "blockexplorer/nodes"
    And I want to limit to 2
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
      [
        {
          "account_count": 6,
          "balance": 600000000000,
          "hash": "hash6",
          "id": "0006",
          "ipv4": "191.123.21.23",
          "message_hash": "messageHash6",
          "msid": "6",
          "mtim": "2018-07-30T15:00:00+02:00",
          "port": 80,
          "public_key": "publicKey",
          "status": 6
        },
        {
          "account_count": 5,
          "balance": 500000000000,
          "hash": "hash5",
          "id": "0005",
          "ipv4": "191.123.21.23",
          "message_hash": "messageHash5",
          "msid": "5",
          "mtim": "2018-07-30T15:00:00+02:00",
          "port": 80,
          "public_key": "publicKey",
          "status": 5
        }
      ]
    """

  Scenario: List all available nodes with limit, offset and sort by id asc
    Given I want to get the list of "blockexplorer/nodes"
    And I want to limit to 2
    And I want to offset to 3
    And I want to sort by "id"
    And I want to order by "asc"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
      [
        {
          "account_count": 4,
          "balance": 400000000000,
          "hash": "hash4",
          "id": "0004",
          "ipv4": "191.123.21.23",
          "message_hash": "messageHash4",
          "msid": "4",
          "mtim": "2018-07-30T15:00:00+02:00",
          "port": 80,
          "public_key": "publicKey",
          "status": 4
        },
        {
          "account_count": 5,
          "balance": 500000000000,
          "hash": "hash5",
          "id": "0005",
          "ipv4": "191.123.21.23",
          "message_hash": "messageHash5",
          "msid": "5",
          "mtim": "2018-07-30T15:00:00+02:00",
          "port": 80,
          "public_key": "publicKey",
          "status": 5
        }
      ]
    """

  Scenario: Unable to get list of nodes with invalid sort field
    Given I want to get the list of "blockexplorer/nodes"
    And I want to sort by "test"
    When I request resource
    Then the response status code should be 400
    And the response should contain:
    """
        {
          "code": 400,
          "message": "Sort value `test` is invalid. Only id, balance values are supported."
        }
    """

  Scenario: Unable to get list of nodes with invalid order field
    Given I want to get the list of "blockexplorer/nodes"
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

  Scenario: Unable to get list of nodes with invalid limit field
    Given I want to get the list of "blockexplorer/nodes"
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

  Scenario: Unable to get list of nodes with invalid offset field
    Given I want to get the list of "blockexplorer/nodes"
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

  Scenario: Get single node
    Given I want to get the resource "blockexplorer/nodes" with id "0001"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
        {
          "account_count": 1,
          "balance": 100000000000,
          "hash": "hash1",
          "id": "0001",
          "ipv4": "191.123.21.23",
          "message_hash": "messageHash1",
          "msid": "1",
          "mtim": "2018-07-30T15:00:00+02:00",
          "port": 80,
          "public_key": "publicKey",
          "status": 1
        }
    """

  Scenario: Unable to get non-existent resource
    Given I want to get the resource "blockexplorer/nodes" with id "1111"
    When I request resource
    Then the response status code should be 404
    And the response should contain:
    """
        {
          "code": 404,
          "message": "The requested resource: 1111 was not found"
        }
    """

  Scenario: Unable to get the resource by invalid id
    Given I want to get the resource "blockexplorer/nodes" with id "123-22"
    When I request resource
    Then the response status code should be 422
    And the response should contain:
    """
        {
          "code": 422,
          "message": "Invalid resource identity"
        }
    """
