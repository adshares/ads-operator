Feature: Transactions
  In order to display transactions in blockexplorer
  As an API client
  I need to be able to fetch single transaction and list of transactions

  Background:
    Given "broadcastTransaction" exist in application:
      | id                 | blockId  | messageId     | nodeId | type        | size | message | messageLength | msgId          | node | signature | time                          | user |
      | 0001:00000001:0000 | 00000001 | 0001:00000001 | 0001   | broadcast   | 01   | test_01 | test_01       | 0001:00000001  | 0001 | 0001      | 2018-07-31T08:49:36.000+02:00 | 01_u |
      | 0002:00000011:0000 | 00000011 | 0002:00000011 | 0002   | broadcast   | 11   | test_11 | test_11       | 0002:00000011  | 0002 | 0011      | 2018-07-31T08:49:36.000+02:00 | 11_u |
      | 0003:00000021:0000 | 00000021 | 0003:00000021 | 0003   | broadcast   | 21   | test_21 | test_21       | 0003:00000021  | 0003 | 0021      | 2018-07-31T08:49:36.000+02:00 | 21_u |
    Given "connectionTransaction" exist in application:
      | id                 | blockId  | messageId     | nodeId | type        | size | ipAddress    | port |
      | 0001:00000002:0000 | 00000002 | 0001:00000002 | 0001   | connection  | 02   | 192.168.1.2  | 80   |
      | 0002:00000012:0000 | 00000012 | 0002:00000012 | 0002   | connection  | 12   | 192.168.1.12 | 80   |
      | 0003:00000022:0000 | 00000022 | 0003:00000022 | 0003   | connection  | 22   | 192.168.1.22 | 80   |
    Given "emptyTransaction" exist in application:
      | id                 | blockId  | messageId     | nodeId | type        | size |
      | 0001:00000003:0000 | 00000003 | 0001:00000003 | 0001   | empty       | 03   |
      | 0002:00000013:0000 | 00000013 | 0002:00000013 | 0002   | empty       | 13   |
      | 0003:00000023:0000 | 00000023 | 0003:00000023 | 0003   | empty       | 23   |
    Given "keyTransaction" exist in application:
      | id                 | blockId  | messageId     | nodeId | type        | size | msgId         | newPublicKey                                                     | oldPublicKey                                                     | publicKey                                                        | publicKeySignature                                               | targetNode | targetUser | node | signature | time                          | user |
      | 0001:00000004:0000 | 00000004 | 0001:00000004 | 0001   | key         | 04   | 0001:00000004 | A9C0D972D8AAB73805EC4A28291E052E3B5FAFE0ADC9D724917054E5E2690363 | 3A9F69FEDCB0694123899C1EA7E14256CAD7B4A2C86311FC6B5ED4027404A282 | 9D46567A482F8F6AA567804EF2274F6ACEB370D8F7461C3A0DF4CE2C0DF432EB | 9D46567A482F8F6AA567804EF2274F6ACEB370D8F7461C3A0DF4CE2C0DF432EB | 04_tN      | 04_tU      | 0001 | 04_s      | 2018-07-31T08:49:36.000+02:00 | 04_u |
      | 0002:00000014:0000 | 00000014 | 0002:00000014 | 0002   | key         | 14   | 0002:00000014 | 3A9F69FEDCB0694123899C1EA7E14256CAD7B4A2C86311FC6B5ED4027404A282 | 9D46567A482F8F6AA567804EF2274F6ACEB370D8F7461C3A0DF4CE2C0DF432EB | A9C0D972D8AAB73805EC4A28291E052E3B5FAFE0ADC9D724917054E5E2690363 | A9C0D972D8AAB73805EC4A28291E052E3B5FAFE0ADC9D724917054E5E2690363 | 14_tN      | 14_tU      | 0002 | 14_s      | 2018-07-31T08:49:36.000+02:00 | 14_u |
      | 0003:00000024:0000 | 00000024 | 0003:00000024 | 0003   | key         | 24   | 0003:00000024 | 9D46567A482F8F6AA567804EF2274F6ACEB370D8F7461C3A0DF4CE2C0DF432EB | A9C0D972D8AAB73805EC4A28291E052E3B5FAFE0ADC9D724917054E5E2690363 | 3A9F69FEDCB0694123899C1EA7E14256CAD7B4A2C86311FC6B5ED4027404A282 | 3A9F69FEDCB0694123899C1EA7E14256CAD7B4A2C86311FC6B5ED4027404A282 | 24_tN      | 24_tU      | 0003 | 14_s      | 2018-07-31T08:49:36.000+02:00 | 24_u |
    Given "logAccountTransaction" exist in application:
      | id                 | blockId  | messageId     | nodeId | type        | size | msgId         | node | signature | time                          | user |
      | 0001:00000005:0000 | 00000005 | 0001:00000115 | 0001   | log_account | 0005 | 0001:00000005 | 0001 | 0005      | 2018-07-31T08:49:36.000+02:00 | 05_u |
      | 0002:00000015:0000 | 00000015 | 0002:00000015 | 0002   | log_account | 0015 | 0002:00000015 | 0002 | 0015      | 2018-07-31T08:49:36.000+02:00 | 15_u |
      | 0003:00000025:0000 | 00000025 | 0003:00000025 | 0003   | log_account | 0025 | 0003:00000025 | 0003 | 0025      | 2018-07-31T08:49:36.000+02:00 | 25_u |
    Given "sendOneTransaction" exist in application:
      | id                 | blockId  | messageId     | nodeId | type        | size | msgId         | node | signature | time                          | user | amount | message  | senderAddress | senderFee | targetAddress | targetNode | targetUser |
      | 0001:00000006:0000 | 00000006 | 0001:00000006 | 0001   | send_one    | 06   | 0001:00000006 | 0001 | 0006      | 2018-07-31T08:49:36.000+02:00 | 06_u | 06_a   | 06_m     | 06_sA         | 06_sF     | 06_tA         | 06_tN      | 06_tU      |
      | 0002:00000016:0000 | 00000016 | 0002:00000016 | 0002   | send_one    | 16   | 0002:00000016 | 0002 | 0016      | 2018-07-31T08:49:36.000+02:00 | 16_u | 16_a   | 16_m     | 16_sA         | 16_sF     | 16_tA         | 16_tN      | 16_tU      |
      | 0003:00000026:0000 | 00000026 | 0003:00000026 | 0003   | send_one    | 26   | 0003:00000026 | 0003 | 0026      | 2018-07-31T08:49:36.000+02:00 | 26_u | 26_a   | 26_m     | 26_sA         | 26_sF     | 26_tA         | 26_tN      | 26_tU      |
    Given "sendManyTransaction" exist in application:
      | id                 | blockId  | messageId     | nodeId | type        | size | msgId         | node | signature | time                          | user | senderAddress | senderFee | wireCount     |
      | 0001:00000007:0000 | 00000007 | 0001:00000007 | 0001   | send_many   | 0007 | 0001:00000007 | 0001 | 0007      | 2018-07-31T08:49:36.000+02:00 | 07_u | 07_sA         | 07_sF     | amount        |
      | 0002:00000017:0000 | 00000017 | 0002:00000017 | 0002   | send_many   | 0017 | 0002:00000017 | 0002 | 0017      | 2018-07-31T08:49:36.000+02:00 | 17_u | 17_sA         | 17_sF     | targetAddress |
      | 0003:00000027:0000 | 00000027 | 0003:00000027 | 0003   | send_many   | 0027 | 0003:00000027 | 0003 | 0027      | 2018-07-31T08:49:36.000+02:00 | 27_u | 27_sA         | 27_sF     | targetNode    |
      | 0004:00000037:0000 | 00000037 | 0004:00000037 | 0004   | send_many   | 0037 | 0004:00000037 | 0004 | 0037      | 2018-07-31T08:49:36.000+02:00 | 37_u | 37_sA         | 37_sF     | targetUser    |
    Given "statusTransaction" exist in application:
      | id                 | blockId  | messageId     | nodeId | type        | size | msgId         | status | node | signature | time                              | user | targetNode | targetUser |
      | 0001:00000008:0000 | 00000008 | 0001:00000008 | 0001   | status      | 0008 | 0001:00000008 | 08_s   | 0001 | 0008      | 2018-07-31T08:49:36.000+02:00     | 08_u | 08_tN      | 08_tU      |
      | 0002:00000018:0000 | 00000018 | 0002:00000018 | 0002   | status      | 0018 | 0002:00000018 | 18_s   | 0002 | 0018      | 2018-07-31T08:49:36.000+02:00     | 18_u | 18_tN      | 18_tU      |
      | 0003:00000028:0000 | 00000028 | 0003:00000028 | 0003   | status      | 0028 | 0003:00000028 | 28_s   | 0003 | 0028      | 2018-07-31T08:49:36.000+02:00     | 28_u | 28_tN      | 28_tU      |

  Scenario: List all available transaction without sort and pagination
    Given I want to get the list of "blockexplorer/transactions"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
      [
        {
        "block_id":"00000037",
        "id":"0004:00000037:0000",
        "message_id":"0004:00000037",
        "node_id":"0004",
        "type":"send_many",
        "size":37,
        "msg_id":"4",
        "node":4,
        "sender_address":"37_sA",
        "sender_fee":37,
        "signature":"0037",
        "time":"2018-07-31T08:49:36+02:00",
        "user":37,
        "wire_count":0,
        "wires":[]
        },
        {
        "block_id":"00000028",
        "id":"0003:00000028:0000",
        "message_id":"0003:00000028",
        "node_id":"0003",
        "type":"status",
        "size":28,
        "msg_id":"3",
        "node":3,
        "signature":"0028",
        "status":28,
        "target_node":28,
        "target_user":28,
        "time":"2018-07-31T08:49:36+02:00",
        "user":28
        },
        {
        "block_id":"00000027",
        "id":"0003:00000027:0000",
        "message_id":"0003:00000027",
        "node_id":"0003",
        "type":"send_many",
        "size":27,
        "msg_id":"3",
        "node":3,
        "sender_address":"27_sA",
        "sender_fee":27,
        "signature":"0027",
        "time":"2018-07-31T08:49:36+02:00",
        "user":27,
        "wire_count":0,
        "wires":[]
        },
        {
        "block_id":"00000026",
        "id":"0003:00000026:0000",
        "message_id":"0003:00000026",
        "node_id":"0003",
        "type":"send_one",
        "size":26,
        "amount":"26",
        "message":26,
        "msg_id":"3",
        "node":3,
        "sender_address":"26_sA",
        "sender_fee":26,
        "signature":"0026",
        "target_address":"26_tA",
        "target_node":26,
        "target_user":26,
        "time":"2018-07-31T08:49:36+02:00",
        "user":26
        },
        {
        "block_id":"00000025",
        "id":"0003:00000025:0000",
        "message_id":"0003:00000025",
        "node_id":"0003",
        "type":"log_account",
        "size":25,
        "msg_id":"3",
        "node":3,
        "signature":"0025",
        "time":"2018-07-31T08:49:36+02:00",
        "user":25
        },
        {
        "block_id":"00000024",
        "id":"0003:00000024:0000",
        "message_id":"0003:00000024",
        "node_id":"0003",
        "type":"key",
        "size":24,
        "msg_id":"3",
        "new_public_key":"9D46567A482F8F6AA567804EF2274F6ACEB370D8F7461C3A0DF4CE2C0DF432EB",
        "node":3,
        "old_public_key":"A9C0D972D8AAB73805EC4A28291E052E3B5FAFE0ADC9D724917054E5E2690363",
        "public_key":"3A9F69FEDCB0694123899C1EA7E14256CAD7B4A2C86311FC6B5ED4027404A282",
        "public_key_signature":"3A9F69FEDCB0694123899C1EA7E14256CAD7B4A2C86311FC6B5ED4027404A282",
        "signature":"14_s",
        "target_node":"24",
        "target_user":"24",
        "time":"2018-07-31T08:49:36+02:00",
        "user":24
        },
        {
        "block_id":"00000023",
        "id":"0003:00000023:0000",
        "message_id":"0003:00000023",
        "node_id":"0003",
        "type":"empty",
        "size":23
        },
        {
        "block_id":"00000022",
        "id":"0003:00000022:0000",
        "message_id":"0003:00000022",
        "node_id":"0003",
        "type":"connection",
        "size":22,
        "ip_address":"192.168.1.22",
        "port":80
        },
        {
        "block_id":"00000021",
        "id":"0003:00000021:0000",
        "message_id":"0003:00000021",
        "node_id":"0003",
        "type":"broadcast",
        "size":21,
        "message":"test_21",
        "message_length":0,
        "msg_id":"3",
        "node":3,
        "signature":"0021",
        "time":"2018-07-31T08:49:36+02:00",
        "user":21
        },
        {
        "block_id":"00000018",
        "id":"0002:00000018:0000",
        "message_id":"0002:00000018",
        "node_id":"0002",
        "type":"status",
        "size":18,
        "msg_id":"2",
        "node":2,
        "signature":"0018",
        "status":18,
        "target_node":18,
        "target_user":18,
        "time":"2018-07-31T08:49:36+02:00",
        "user":18
        },
        {
        "block_id":"00000017",
        "id":"0002:00000017:0000",
        "message_id":"0002:00000017",
        "node_id":"0002",
        "type":"send_many",
        "size":17,
        "msg_id":"2",
        "node":2,
        "sender_address":"17_sA",
        "sender_fee":17,
        "signature":"0017",
        "time":"2018-07-31T08:49:36+02:00",
        "user":17,
        "wire_count":0,
        "wires":[]
        },
        {
        "block_id":"00000016",
        "id":"0002:00000016:0000",
        "message_id":"0002:00000016",
        "node_id":"0002",
        "type":"send_one",
        "size":16,
        "amount":"16",
        "message":16,
        "msg_id":"2",
        "node":2,
        "sender_address":"16_sA",
        "sender_fee":16,
        "signature":"0016",
        "target_address":"16_tA",
        "target_node":16,
        "target_user":16,
        "time":"2018-07-31T08:49:36+02:00",
        "user":16
        },
        {
        "block_id":"00000015",
        "id":"0002:00000015:0000",
        "message_id":"0002:00000015",
        "node_id":"0002",
        "type":"log_account",
        "size":15,
        "msg_id":"2",
        "node":2,
        "signature":"0015",
        "time":"2018-07-31T08:49:36+02:00",
        "user":15
        },
        {
        "block_id":"00000014",
        "id":"0002:00000014:0000",
        "message_id":"0002:00000014",
        "node_id":"0002",
        "type":"key",
        "size":14,
        "msg_id":"2",
        "new_public_key":"3A9F69FEDCB0694123899C1EA7E14256CAD7B4A2C86311FC6B5ED4027404A282",
        "node":2,
        "old_public_key":"9D46567A482F8F6AA567804EF2274F6ACEB370D8F7461C3A0DF4CE2C0DF432EB",
        "public_key":"A9C0D972D8AAB73805EC4A28291E052E3B5FAFE0ADC9D724917054E5E2690363",
        "public_key_signature":"A9C0D972D8AAB73805EC4A28291E052E3B5FAFE0ADC9D724917054E5E2690363",
        "signature":"14_s",
        "target_node":"14",
        "target_user":"14",
        "time":"2018-07-31T08:49:36+02:00",
        "user":14
        },
        {
        "block_id":"00000013",
        "id":"0002:00000013:0000",
        "message_id":"0002:00000013",
        "node_id":"0002",
        "type":"empty",
        "size":13
        },
        {
        "block_id":"00000012",
        "id":"0002:00000012:0000",
        "message_id":"0002:00000012",
        "node_id":"0002",
        "type":"connection",
        "size":12,
        "ip_address":"192.168.1.12",
        "port":80
        },
        {
        "block_id":"00000011",
        "id":"0002:00000011:0000",
        "message_id":"0002:00000011",
        "node_id":"0002",
        "type":"broadcast",
        "size":11,
        "message":"test_11",
        "message_length":0,
        "msg_id":"2",
        "node":2,
        "signature":"0011",
        "time":"2018-07-31T08:49:36+02:00",
        "user":11
        },
        {
        "block_id":"00000008",
        "id":"0001:00000008:0000",
        "message_id":"0001:00000008",
        "node_id":"0001",
        "type":"status",
        "size":8,
        "msg_id":"1",
        "node":1,
        "signature":"0008",
        "status":8,
        "target_node":8,
        "target_user":8,
        "time":"2018-07-31T08:49:36+02:00",
        "user":8
        },
        {
        "block_id":"00000007",
        "id":"0001:00000007:0000",
        "message_id":"0001:00000007",
        "node_id":"0001",
        "type":"send_many",
        "size":7,
        "msg_id":"1",
        "node":1,
        "sender_address":"07_sA",
        "sender_fee":7,
        "signature":"0007",
        "time":"2018-07-31T08:49:36+02:00",
        "user":7,
        "wire_count":0,
        "wires":[]
        },
        {
        "block_id":"00000006",
        "id":"0001:00000006:0000",
        "message_id":"0001:00000006",
        "node_id":"0001",
        "type":"send_one",
        "size":6,
        "amount":"6",
        "message":6,
        "msg_id":"1",
        "node":1,
        "sender_address":"06_sA",
        "sender_fee":6,
        "signature":"0006",
        "target_address":"06_tA",
        "target_node":6,
        "target_user":6,
        "time":"2018-07-31T08:49:36+02:00",
        "user":6
        },
        {
        "block_id":"00000005",
        "id":"0001:00000005:0000",
        "message_id":"0001:00000115",
        "node_id":"0001",
        "type":"log_account",
        "size":5,
        "msg_id":"1",
        "node":1,
        "signature":"0005",
        "time":"2018-07-31T08:49:36+02:00",
        "user":5
        },
        {
        "block_id":"00000004",
        "id":"0001:00000004:0000",
        "message_id":"0001:00000004",
        "node_id":"0001",
        "type":"key",
        "size":4,
        "msg_id":"1",
        "new_public_key":"A9C0D972D8AAB73805EC4A28291E052E3B5FAFE0ADC9D724917054E5E2690363",
        "node":1,
        "old_public_key":"3A9F69FEDCB0694123899C1EA7E14256CAD7B4A2C86311FC6B5ED4027404A282",
        "public_key":"9D46567A482F8F6AA567804EF2274F6ACEB370D8F7461C3A0DF4CE2C0DF432EB",
        "public_key_signature":"9D46567A482F8F6AA567804EF2274F6ACEB370D8F7461C3A0DF4CE2C0DF432EB",
        "signature":"04_s",
        "target_node":"4",
        "target_user":"4",
        "time":"2018-07-31T08:49:36+02:00",
        "user":4
        },
        {
        "block_id":"00000003",
        "id":"0001:00000003:0000",
        "message_id":"0001:00000003",
        "node_id":"0001",
        "type":"empty",
        "size":3
        },
        {
        "block_id":"00000002",
        "id":"0001:00000002:0000",
        "message_id":"0001:00000002",
        "node_id":"0001",
        "type":"connection",
        "size":2,
        "ip_address":"192.168.1.2",
        "port":80
        },
        {
        "block_id":"00000001",
        "id":"0001:00000001:0000",
        "message_id":"0001:00000001",
        "node_id":"0001",
        "type":"broadcast",
        "size":1,
        "message":"test_01",
        "message_length":0,
        "msg_id":"1",
        "node":1,
        "signature":"0001",
        "time":"2018-07-31T08:49:36+02:00",
        "user":1
        }
      ]
    """

  Scenario: List all available transactions with limit
    Given I want to get the list of "blockexplorer/transactions"
    And I want to limit to 3
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
      [
        {
        "block_id":"00000037",
        "id":"0004:00000037:0000",
        "message_id":"0004:00000037",
        "node_id":"0004",
        "type":"send_many",
        "size":37,
        "msg_id":"4",
        "node":4,
        "sender_address":"37_sA",
        "sender_fee":37,
        "signature":"0037",
        "time":"2018-07-31T08:49:36+02:00",
        "user":37,
        "wire_count":0,
        "wires":[]
        },
        {
        "block_id":"00000028",
        "id":"0003:00000028:0000",
        "message_id":"0003:00000028",
        "node_id":"0003",
        "type":"status",
        "size":28,
        "msg_id":"3",
        "node":3,
        "signature":"0028",
        "status":28,
        "target_node":28,
        "target_user":28,
        "time":"2018-07-31T08:49:36+02:00",
        "user":28
        },
        {
        "block_id":"00000027",
        "id":"0003:00000027:0000",
        "message_id":"0003:00000027",
        "node_id":"0003",
        "type":"send_many",
        "size":27,
        "msg_id":"3",
        "node":3,
        "sender_address":"27_sA",
        "sender_fee":27,
        "signature":"0027",
        "time":"2018-07-31T08:49:36+02:00",
        "user":27,
        "wire_count":0,
        "wires":[]
        }
      ]
    """

  Scenario: List all available transactions with sort by asc
    Given I want to get the list of "blockexplorer/transactions"
    And I want to limit to 5
    And I want to order by "asc"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
        """
      [
          {
          "block_id":"00000001",
          "id":"0001:00000001:0000",
          "message_id":"0001:00000001",
          "node_id":"0001",
          "type":"broadcast",
          "size":1,
          "message":"test_01",
          "message_length":0,
          "msg_id":"1",
          "node":1,
          "signature":"0001",
          "time":"2018-07-31T08:49:36+02:00",
          "user":1
          },
          {
          "block_id":"00000002",
          "id":"0001:00000002:0000",
          "message_id":"0001:00000002",
          "node_id":"0001",
          "type":"connection",
          "size":2,
          "ip_address":"192.168.1.2",
          "port":80
          },
          {
          "block_id":"00000003",
          "id":"0001:00000003:0000",
          "message_id":"0001:00000003",
          "node_id":"0001",
          "type":"empty",
          "size":3
          },
          {
          "block_id":"00000004",
          "id":"0001:00000004:0000",
          "message_id":"0001:00000004",
          "node_id":"0001",
          "type":"key",
          "size":4,
          "msg_id":"1",
          "new_public_key":"A9C0D972D8AAB73805EC4A28291E052E3B5FAFE0ADC9D724917054E5E2690363",
          "node":1,
          "old_public_key":"3A9F69FEDCB0694123899C1EA7E14256CAD7B4A2C86311FC6B5ED4027404A282",
          "public_key":"9D46567A482F8F6AA567804EF2274F6ACEB370D8F7461C3A0DF4CE2C0DF432EB",
          "public_key_signature":"9D46567A482F8F6AA567804EF2274F6ACEB370D8F7461C3A0DF4CE2C0DF432EB",
          "signature":"04_s",
          "target_node":"4",
          "target_user":"4",
          "time":"2018-07-31T08:49:36+02:00",
          "user":4
          },
          {
          "block_id":"00000005",
          "id":"0001:00000005:0000",
          "message_id":"0001:00000115",
          "node_id":"0001",
          "type":"log_account",
          "size":5,
          "msg_id":"1",
          "node":1,
          "signature":"0005",
          "time":"2018-07-31T08:49:36+02:00",
          "user":5
          }
      ]
    """

  Scenario: List all available transactions with sort by id
    Given I want to get the list of "blockexplorer/transactions"
    And I want to limit to 2
    And I want to sort by "id"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
      [
        {
        "block_id":"00000037",
        "id":"0004:00000037:0000",
        "message_id":"0004:00000037",
        "node_id":"0004",
        "type":"send_many",
        "size":37,
        "msg_id":"4",
        "node":4,
        "sender_address":"37_sA",
        "sender_fee":37,
        "signature":"0037",
        "time":"2018-07-31T08:49:36+02:00",
        "user":37,
        "wire_count":0,
        "wires":[]
        },
        {
        "block_id":"00000028",
        "id":"0003:00000028:0000",
        "message_id":"0003:00000028",
        "node_id":"0003",
        "type":"status",
        "size":28,
        "msg_id":"3",
        "node":3,
        "signature":"0028",
        "status":28,
        "target_node":28,
        "target_user":28,
        "time":"2018-07-31T08:49:36+02:00",
        "user":28
        }
      ]
    """

  Scenario: List all available transactions with sort by asc
    Given I want to get the list of "blockexplorer/transactions"
    And I want to limit to 2
    And I want to order by "asc"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
      [
        {
        "block_id":"00000001",
        "id":"0001:00000001:0000",
        "message_id":"0001:00000001",
        "node_id":"0001",
        "type":"broadcast",
        "size":1,
        "message":"test_01",
        "message_length":0,
        "msg_id":"1",
        "node":1,
        "signature":"0001",
        "time":"2018-07-31T08:49:36+02:00",
        "user":1
        },
        {
        "block_id":"00000002",
        "id":"0001:00000002:0000",
        "message_id":"0001:00000002",
        "node_id":"0001",
        "type":"connection",
        "size":2,
        "ip_address":"192.168.1.2",
        "port":80
        }
      ]
    """

  Scenario: List all available transactions with offset and sort by id asc
    Given I want to get the list of "blockexplorer/transactions"
    And I want to offset to 22
    And I want to sort by "id"
    And I want to order by "asc"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
      [
        {
        "block_id":"00000027",
        "id":"0003:00000027:0000",
        "message_id":"0003:00000027",
        "node_id":"0003",
        "type":"send_many",
        "size":27,
        "msg_id":"3",
        "node":3,
        "sender_address":"27_sA",
        "sender_fee":27,
        "signature":"0027",
        "time":"2018-07-31T08:49:36+02:00",
        "user":27,
        "wire_count":0,
        "wires":[]
        },
        {
        "block_id":"00000028",
        "id":"0003:00000028:0000",
        "message_id":"0003:00000028",
        "node_id":"0003",
        "type":"status",
        "size":28,
        "msg_id":"3",
        "node":3,
        "signature":"0028",
        "status":28,
        "target_node":28,
        "target_user":28,
        "time":"2018-07-31T08:49:36+02:00",
        "user":28
        },
        {
        "block_id":"00000037",
        "id":"0004:00000037:0000",
        "message_id":"0004:00000037",
        "node_id":"0004",
        "type":"send_many",
        "size":37,
        "msg_id":"4",
        "node":4,
        "sender_address":"37_sA",
        "sender_fee":37,
        "signature":"0037",
        "time":"2018-07-31T08:49:36+02:00",
        "user":37,
        "wire_count":0,
        "wires":[]
        }
      ]
    """

  Scenario: List all available transactions with limit, offset and sort by id asc
    Given I want to get the list of "blockexplorer/transactions"
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
        "block_id":"00000003",
        "id":"0001:00000003:0000",
        "message_id":"0001:00000003",
        "node_id":"0001",
        "type":"empty",
        "size":3
        },
        {
        "block_id":"00000004",
        "id":"0001:00000004:0000",
        "message_id":"0001:00000004",
        "node_id":"0001",
        "type":"key",
        "size":4,
        "msg_id":"1",
        "new_public_key":"A9C0D972D8AAB73805EC4A28291E052E3B5FAFE0ADC9D724917054E5E2690363",
        "node":1,
        "old_public_key":"3A9F69FEDCB0694123899C1EA7E14256CAD7B4A2C86311FC6B5ED4027404A282",
        "public_key":"9D46567A482F8F6AA567804EF2274F6ACEB370D8F7461C3A0DF4CE2C0DF432EB",
        "public_key_signature":"9D46567A482F8F6AA567804EF2274F6ACEB370D8F7461C3A0DF4CE2C0DF432EB",
        "signature":"04_s",
        "target_node":"4",
        "target_user":"4",
        "time":"2018-07-31T08:49:36+02:00",
        "user":4
        }
      ]
    """

  Scenario: Unable to get list of transactions with invalid sort field
    Given I want to get the list of "blockexplorer/transactions"
    And I want to sort by "test"
    When I request resource
    Then the response status code should be 400
    And the response should contain:
    """
        {
          "code":400,
          "message":"Sort value `test` is invalid. Only id, blockId, type values are supported."
        }
    """

  Scenario: Unable to get list of transactions with invalid order field
    Given I want to get the list of "blockexplorer/transactions"
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

  Scenario: Unable to get list of transactions with invalid limit field
    Given I want to get the list of "blockexplorer/transactions"
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

  Scenario: Unable to get list of transactions with invalid offset field
    Given I want to get the list of "blockexplorer/transactions"
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

  Scenario: Get single transactions
    Given I want to get the resource "blockexplorer/transactions" with id "0003:00000023:0000"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
        {
        "block_id":"00000023",
        "id":"0003:00000023:0000",
        "message_id":"0003:00000023",
        "node_id":"0003",
        "type":"empty",
        "size":23
        }
    """

  Scenario: Unable to get non-existent resource
    Given I want to get the resource "blockexplorer/transactions" with id "0000:00000000:1111"
    When I request resource
    Then the response status code should be 404
    And the response should contain:
    """
        {
          "code": 404,
          "message": "The requested resource: 0000:00000000:1111 was not found"
        }
    """

  Scenario: Unable to get the resource by invalid id
    Given I want to get the resource "blockexplorer/transactions" with id "123-22"
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
    Given I want to get the resource "blockexplorer/transactions" with id "0001*"
    When I request resource
    Then the response status code should be 422
    And the response should contain:
    """
        {
          "code":422,
          "message":"Invalid resource identity"
        }
    """