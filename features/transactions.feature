Feature: Transactions
  In order to display accounts in blockexplorer
  As an API client
  I need to be able to fetch single transaction and list of transactions

  Background:
    Given "emptyTransactions" exist in application:
      | id   | blockId | messageId | nodeId | type    | size |
      | 0001 | 0001    | 0001      | 0001   | empty   | 0    |

    Given "connectionTransactions" exist in application:
      | id   | blockId | messageId | nodeId | type        | size | ipAddress    | port |
      | 0002 | 0001    | 0001      | 0001   | connection  | 0    | 192.168.1.2  | 80   |

  Scenario: List all available transaction without sort and pagination
    Given I want to get the list of "blockexplorer/transactions"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
      [
        {
          "block_id":"0001",
          "id":"0002",
          "message_id":"0001",
          "node_id":"0001",
          "type":"connection",
          "size":0,
          "ip_address":"192.168.1.2",
          "port":80
        },
        {
          "block_id":"0001",
          "id":"0001",
          "message_id":"0001",
          "node_id":"0001",
          "type":"empty",
          "size":0
        }
      ]
    """
