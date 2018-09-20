Feature: Transactions
  In order to display transactions in blockexplorer
  As an API client
  I need to be able to fetch single transaction and list of transactions

  Background:
    Given "broadcastTransaction" exist in application:
      | id                 | blockId  | messageId     | nodeId | type            | size | message                                                          | messageLength                                                    | msgId          | node | signature | time                          | user |
      | 0001:00000001:0000 | 1A7F0400 | 0001:00000001 | 0001   | broadcast       | 01   | 0000000000000000000000000000000000000000000000000000000000000001 | 0000000000000000000000000000000000000000000000000000000000000001 | 0001:00000001  | 0001 | 0001      | 2018-07-31T08:49:36.000+02:00 | 0001 |
      | 0002:00000011:0000 | 1B7F0400 | 0001:00000001 | 0002   | broadcast       | 11   | 0000000000000000000000000000000000000000000000000000000000000011 | 0000000000000000000000000000000000000000000000000000000000000011 | 0002:00000011  | 0002 | 0011      | 2018-07-31T08:49:36.000+02:00 | 0011 |
      | 0003:00000021:0000 | 1C7F0400 | 0001:00000001 | 0003   | broadcast       | 21   | 0000000000000000000000000000000000000000000000000000000000000021 | 0000000000000000000000000000000000000000000000000000000000000021 | 0003:00000021  | 0003 | 0021      | 2018-07-31T08:49:36.000+02:00 | 0021 |
    Given "connectionTransaction" exist in application:
      | id                 | blockId  | messageId     | nodeId | type            | size | ipAddress    | port |
      | 0001:00000002:0000 | 2A7F0400 | 0001:00000002 | 0001   | connection      | 02   | 192.168.1.2  | 80   |
      | 0002:00000012:0000 | 2B7F0400 | 0002:00000012 | 0002   | connection      | 12   | 192.168.1.12 | 80   |
      | 0003:00000022:0000 | 2C7F0400 | 0003:00000022 | 0003   | connection      | 22   | 192.168.1.22 | 80   |
    Given "emptyTransaction" exist in application:
      | id                 | blockId  | messageId     | nodeId | type            | size |
      | 0001:00000003:0000 | 3A7F0400 | 0001:00000003 | 0001   | empty           | 03   |
      | 0002:00000013:0000 | 3B7F0400 | 0002:00000013 | 0002   | empty           | 13   |
      | 0003:00000023:0000 | 3C7F0400 | 0003:00000023 | 0003   | empty           | 23   |
    Given "keyTransaction" exist in application:
      | id                 | blockId  | messageId     | nodeId | type            | size | msgId         | newPublicKey                                                     | oldPublicKey                                                     | publicKey                                                        | publicKeySignature                                               | targetNode | targetUser | node | signature                                                                                                                        | time                          | user |
      | 0001:00000004:0000 | 4A7F0400 | 0001:00000004 | 0001   | account_created | 04   | 0001:00000004 | A9C0D972D8AAB73805EC4A28291E052E3B5FAFE0ADC9D724917054E5E2690363 | 3A9F69FEDCB0694123899C1EA7E14256CAD7B4A2C86311FC6B5ED4027404A282 | 9D46567A482F8F6AA567804EF2274F6ACEB370D8F7461C3A0DF4CE2C0DF432EB | 9D46567A482F8F6AA567804EF2274F6ACEB370D8F7461C3A0DF4CE2C0DF432EB | 0001       | 0004       | 0001 | EE54B7563E8BF13BA244725F723EEA55CC90FA5FCFAF69A0FA26EF7175E67D8BAA678F6E31FF428002C047486A56B12273914B3B1E570882613B0B6C42C67104 | 2018-07-31T08:49:36.000+02:00 | 0004 |
      | 0002:00000014:0000 | 4B7F0400 | 0002:00000014 | 0002   | account_created | 14   | 0002:00000014 | 3A9F69FEDCB0694123899C1EA7E14256CAD7B4A2C86311FC6B5ED4027404A282 | 9D46567A482F8F6AA567804EF2274F6ACEB370D8F7461C3A0DF4CE2C0DF432EB | A9C0D972D8AAB73805EC4A28291E052E3B5FAFE0ADC9D724917054E5E2690363 | A9C0D972D8AAB73805EC4A28291E052E3B5FAFE0ADC9D724917054E5E2690363 | 0002       | 0014       | 0002 | 8BEE474A149EAF1C9081A29BE2B71903A7B267DF3749419C14F5E11256E80EF1B657592F40FD275207851B670E3D0B4FA9CD4BF627C83D0C88996178698AA508 | 2018-07-31T08:49:36.000+02:00 | 0014 |
      | 0003:00000024:0000 | 4C7F0400 | 0003:00000024 | 0003   | account_created | 24   | 0003:00000024 | 9D46567A482F8F6AA567804EF2274F6ACEB370D8F7461C3A0DF4CE2C0DF432EB | A9C0D972D8AAB73805EC4A28291E052E3B5FAFE0ADC9D724917054E5E2690363 | 3A9F69FEDCB0694123899C1EA7E14256CAD7B4A2C86311FC6B5ED4027404A282 | 3A9F69FEDCB0694123899C1EA7E14256CAD7B4A2C86311FC6B5ED4027404A282 | 0003       | 0024       | 0003 | 630DC6EC4A80917266051A66738C1B0B18D63FDA895DABB77AD40FB8C64DA2E526A2B2546DEF2CD24354351DBC7A3E280AA4EB594057B725D31FB00A47BFDB0A | 2018-07-31T08:49:36.000+02:00 | 0024 |
    Given "logAccountTransaction" exist in application:
      | id                 | blockId  | messageId     | nodeId | type            | size | msgId         | node | signature | time                          | user |
      | 0001:00000005:0000 | 5A7F0400 | 0001:00000115 | 0001   | log_account     | 0005 | 0001:00000005 | 0001 | 0005      | 2018-07-31T08:49:36.000+02:00 | 0005 |
      | 0002:00000015:0000 | 5B7F0400 | 0002:00000015 | 0002   | log_account     | 0015 | 0002:00000015 | 0002 | 0015      | 2018-07-31T08:49:36.000+02:00 | 0015 |
      | 0003:00000025:0000 | 5C7F0400 | 0003:00000025 | 0003   | log_account     | 0025 | 0003:00000025 | 0003 | 0025      | 2018-07-31T08:49:36.000+02:00 | 0025 |
    Given "sendOneTransaction" exist in application:
      | id                 | blockId  | messageId     | nodeId | type            | size | msgId         | node | signature                                                                                                                        | time                          | user | amount       | message                                                          | senderAddress      | senderFee | targetAddress      | targetNode | targetUser |
      | 0001:00000006:0000 | 6A7F0400 | 0001:00000006 | 0001   | send_one        | 06   | 0001:00000006 | 0001 | EE54B7563E8BF13BA244725F723EEA55CC90FA5FCFAF69A0FA26EF7175E67D8BAA678F6E31FF428002C047486A56B12273914B3B1E570882613B0B6C42C67104 | 2018-07-31T08:49:36.000+02:00 | 0006 | 060000000000 | 0000000000000000000000000000000000000000000000000000000000000006 | 0001-00000006-0000 | 060000000 | 0002-00000016-0000 | 0001       | 0001       |
      | 0002:00000016:0000 | 6B7F0400 | 0002:00000016 | 0002   | send_one        | 16   | 0002:00000016 | 0002 | 8BEE474A149EAF1C9081A29BE2B71903A7B267DF3749419C14F5E11256E80EF1B657592F40FD275207851B670E3D0B4FA9CD4BF627C83D0C88996178698AA508 | 2018-07-31T08:49:36.000+02:00 | 0016 | 160000000000 | 0000000000000000000000000000000000000000000000000000000000000016 | 0002-00000016-0000 | 160000000 | 0003-00000026-0000 | 0002       | 0002       |
      | 0003:00000026:0000 | 6C7F0400 | 0003:00000026 | 0003   | send_one        | 26   | 0003:00000026 | 0003 | 630DC6EC4A80917266051A66738C1B0B18D63FDA895DABB77AD40FB8C64DA2E526A2B2546DEF2CD24354351DBC7A3E280AA4EB594057B725D31FB00A47BFDB0A | 2018-07-31T08:49:36.000+02:00 | 0026 | 260000000000 | 0000000000000000000000000000000000000000000000000000000000000026 | 0003-00000026-0000 | 260000000 | 0001-00000006-0000 | 0003       | 0003       |
    Given "sendManyTransaction" exist in application:
      | id                 | blockId  | messageId     | nodeId | type            | size | msgId         | node | signature | time                          | user | senderAddress      | senderFee | wireCount     |
      | 0001:00000007:0000 | 7A7F0400 | 0001:00000007 | 0001   | send_many       | 0007 | 0001:00000007 | 0001 | 0007      | 2018-07-31T08:49:36.000+02:00 | 0007 | 0001-00000007-0000 | 070000000 | amount        |
      | 0002:00000017:0000 | 7B7F0400 | 0002:00000017 | 0002   | send_many       | 0017 | 0002:00000017 | 0002 | 0017      | 2018-07-31T08:49:36.000+02:00 | 0017 | 0002-00000017-0000 | 170000000 | targetAddress |
      | 0003:00000027:0000 | 7C7F0400 | 0003:00000027 | 0003   | send_many       | 0027 | 0003:00000027 | 0003 | 0027      | 2018-07-31T08:49:36.000+02:00 | 0027 | 0003-00000027-0000 | 270000000 | targetNode    |
      | 0004:00000037:0000 | 7C7F0400 | 0004:00000037 | 0004   | send_many       | 0037 | 0004:00000037 | 0004 | 0037      | 2018-07-31T08:49:36.000+02:00 | 0037 | 0004-00000037-0000 | 370000000 | targetUser    |
    Given "statusTransaction" exist in application:
      | id                 | blockId  | messageId     | nodeId | type            | size | msgId         | status | node | signature | time                              | user | targetNode | targetUser |
      | 0001:00000008:0000 | 8A7F0400 | 0001:00000008 | 0001   | set_node_status | 0008 | 0001:00000008 | 0      | 0001 | 0008      | 2018-07-31T08:49:36.000+02:00     | 0008 | 0008       | 0008       |
      | 0002:00000018:0000 | 8B7F0400 | 0002:00000018 | 0002   | set_node_status | 0018 | 0002:00000018 | 0      | 0002 | 0018      | 2018-07-31T08:49:36.000+02:00     | 0018 | 0018       | 0018       |
      | 0003:00000028:0000 | 8C7F0400 | 0003:00000028 | 0003   | set_node_status | 0028 | 0003:00000028 | 0      | 0003 | 0028      | 2018-07-31T08:49:36.000+02:00     | 0028 | 0028       | 0028       |

  Scenario: List transactions by block id
    Given I want to get the list of "blockexplorer/blocks/7C7F0400/transactions"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
     [
        {
          "block_id":"7C7F0400",
          "id":"0004:00000037:0000",
          "message_id":"0004:00000037",
          "node_id":"0004",
          "type":"send_many",
          "size":37,
          "msg_id":"4",
          "node":4,
          "sender_address":"0004-00000037-0000",
          "sender_fee":9223372036854775807,
          "signature":"0037",
          "time":"2018-07-31T08:49:36+02:00",
          "user":37,
          "wire_count":0,
          "wires":[]
        },
        {
          "block_id":"7C7F0400",
          "id":"0003:00000027:0000",
          "message_id":"0003:00000027",
          "node_id":"0003",
          "type":"send_many",
          "size":27,
          "msg_id":"3",
          "node":3,
          "sender_address":"0003-00000027-0000",
          "sender_fee":9223372036854775807,
          "signature":"0027",
          "time":"2018-07-31T08:49:36+02:00",
          "user":27,
          "wire_count":0,
          "wires":[]
        }
     ]
   """

  Scenario: List transactions by block id
    Given I want to get the list of "blockexplorer/blocks/7C7F0400/transactions"
    And I want to limit to 1
    And I want to offset to 1
    When I request resource
    Then the response status code should be 200
    And the response should contain:
   """
     [
       {
          "block_id":"7C7F0400",
          "id":"0003:00000027:0000",
          "message_id":"0003:00000027",
          "node_id":"0003",
          "type":"send_many",
          "size":27,
          "msg_id":"3",
          "node":3,
          "sender_address":"0003-00000027-0000",
          "sender_fee":9223372036854775807,
          "signature":"0027",
          "time":"2018-07-31T08:49:36+02:00",
          "user":27,
          "wire_count":0,
          "wires":[]
        }
     ]
   """

  Scenario: List transactions by block id
    Given I want to get the list of "blockexplorer/blocks/7C7F0400/transactions"
    And I want to sort by "id"
    And I want to order by "asc"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
   """
     [
        {
          "block_id":"7C7F0400",
          "id":"0003:00000027:0000",
          "message_id":"0003:00000027",
          "node_id":"0003",
          "type":"send_many",
          "size":27,
          "msg_id":"3",
          "node":3,
          "sender_address":"0003-00000027-0000",
          "sender_fee":9223372036854775807,
          "signature":"0027",
          "time":"2018-07-31T08:49:36+02:00",
          "user":27,
          "wire_count":0,
          "wires":[]
        },
        {
          "block_id":"7C7F0400",
          "id":"0004:00000037:0000",
          "message_id":"0004:00000037",
          "node_id":"0004",
          "type":"send_many",
          "size":37,
          "msg_id":"4",
          "node":4,
          "sender_address":"0004-00000037-0000",
          "sender_fee":9223372036854775807,
          "signature":"0037",
          "time":"2018-07-31T08:49:36+02:00",
          "user":37,
          "wire_count":0,
          "wires":[]
        }
     ]
   """

  Scenario: List transactions by invalid block id
    Given I want to get the list of "blockexplorer/blocks/9C7F0400/transactions"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
   """
     []
   """

  Scenario: List transactions by invalid block id
    Given I want to get the list of "blockexplorer/blocks/9C7F040*/transactions"
    When I request resource
    Then the response status code should be 422
    And the response should contain:
   """
      {
        "code":422,
        "message":"Invalid resource identity"
      }
   """

  Scenario: List transactions by invalid block id
    Given I want to get the list of "blockexplorer/blocks/transactions"
    When I request resource
    Then the response status code should be 422
    And the response should contain:
   """
      {
        "code":422,
        "message":"Invalid resource identity"
      }
   """

  Scenario: List transactions by invalid block id
    Given I want to get the list of "blockexplorer/blocks//transactions"
    When I request resource
    Then the response status code should be 404
    And the response should contain:
   """
      {
        "code":404,
        "message":"No route found for \"GET \/api\/v1\/blockexplorer\/blocks\/\/transactions\""
      }
   """