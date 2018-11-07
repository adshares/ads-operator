Feature: Nodes
  In order to display nodes in blockexplorer
  As an API client
  I need to be able to fetch single node and list of nodes

  Background:
    Given "nodes" exist in application:
      | id   | accountCount | balance             | hash                                                             | ipv4          | messageHash                                                      | msid  | mtim                | port  | publicKey                                                        | status  | version |
      | 0001 | 1            | 6559081.01538461530 | 524769EE119CBCC27A8F8DE7D8A55CA12E9773F4763C40394434F522C43A1463 | 191.123.21.23 | 70D11F677A9B8F0A49BAD9DCE9F715FCE4AA76BDF23C14FB40142999A1E84577 | 1     | 2018-07-30 15:00:00 | 80    | A9C0D972D8AAB73805EC4A28291E052E3B5FAFE0ADC9D724917054E5E2690363 | 1       | 0.0.1   |
      | 0002 | 2            | 6559081.01538461530 | 70D11F677A9B8F0A49BAD9DCE9F715FCE4AA76BDF23C14FB40142999A1E84577 | 191.123.21.23 | A534B0451771A35021BF47E09F0865C3E652B78B8104CDF9944A5709870BF63B | 2     | 2018-07-30 15:00:00 | 80    | 3A9F69FEDCB0694123899C1EA7E14256CAD7B4A2C86311FC6B5ED4027404A282 | 2       | 0.0.2   |
      | 0003 | 3            | 5366520.83076923070 | A534B0451771A35021BF47E09F0865C3E652B78B8104CDF9944A5709870BF63B | 191.123.21.23 | 70B8290E9DB0DC611CAAA3E38DF11B7D0E1EC41219AB00CF7B7B628644460429 | 3     | 2018-07-30 15:00:00 | 80    | 9D46567A482F8F6AA567804EF2274F6ACEB370D8F7461C3A0DF4CE2C0DF432EB | 3       | 0.0.3   |
      | 0004 | 4            | 4770240.73846153840 | 70B8290E9DB0DC611CAAA3E38DF11B7D0E1EC41219AB00CF7B7B628644460429 | 191.123.21.23 | 07187918DEC935E75D00B967B8AC8FF350168ED27A9A6ADFE78A7141B60F0313 | 4     | 2018-07-30 15:00:00 | 80    | 6B541CA4AA9B7117AC4D2DB61E487C4CBB52D59554C5E20CCB19767C1DCA5212 | 4       | 0.0.4   |
    Given "nodes" exist in application:
      | id   | accountCount | balance             | hash                                                             | ipv4          | messageHash                                                      | msid  | mtim                | port  | publicKey                                                        | status  |
      | 0005 | 5            | 4173960.64615384610 | 07187918DEC935E75D00B967B8AC8FF350168ED27A9A6ADFE78A7141B60F0313 | 191.123.21.23 | 26710F00488043124564798C1D5B617CE54371C6334D54987FF0991A25A5324D | 5     | 2018-07-30 15:00:00 | 80    | 4D68B719B7976A1BD38DEB6A88A97AE6258B564B13394490740B00257C8D1550 | 5       |
      | 0006 | 6            | 18860.44087591240   | 26710F00488043124564798C1D5B617CE54371C6334D54987FF0991A25A5324D | 191.123.21.23 | 524769EE119CBCC27A8F8DE7D8A55CA12E9773F4763C40394434F522C43A1463 | 6     | 2018-07-30 15:00:00 | 80    | 6431A8580B014DA2420FF32842B0BA3CAB3B77F01D1150E5A0D34743F243B778 | 6       |

  Scenario: List all available nodes without sort and pagination
    Given I want to get the list of "blockexplorer/nodes"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
      [
         {
          "account_count":6,
          "balance":"1886044087591240",
          "hash":"26710F00488043124564798C1D5B617CE54371C6334D54987FF0991A25A5324D",
          "id":"0006",
          "ipv4":"191.123.21.23",
          "message_hash":"524769EE119CBCC27A8F8DE7D8A55CA12E9773F4763C40394434F522C43A1463",
          "msid":"6",
          "mtim":"2018-07-30T15:00:00+02:00",
          "port":80,
          "public_key":"6431A8580B014DA2420FF32842B0BA3CAB3B77F01D1150E5A0D34743F243B778",
          "status":6
        },
        {
          "account_count":5,
          "balance":"417396064615384610",
          "hash":"07187918DEC935E75D00B967B8AC8FF350168ED27A9A6ADFE78A7141B60F0313",
          "id":"0005",
          "ipv4":"191.123.21.23",
          "message_hash":"26710F00488043124564798C1D5B617CE54371C6334D54987FF0991A25A5324D",
          "msid":"5",
          "mtim":"2018-07-30T15:00:00+02:00",
          "port":80,
          "public_key":"4D68B719B7976A1BD38DEB6A88A97AE6258B564B13394490740B00257C8D1550",
          "status":5
        },
        {
          "account_count":4,
          "balance":"477024073846153840",
          "hash":"70B8290E9DB0DC611CAAA3E38DF11B7D0E1EC41219AB00CF7B7B628644460429",
          "id":"0004",
          "ipv4":"191.123.21.23",
          "message_hash":"07187918DEC935E75D00B967B8AC8FF350168ED27A9A6ADFE78A7141B60F0313",
          "msid":"4",
          "mtim":"2018-07-30T15:00:00+02:00",
          "port":80,
          "public_key":"6B541CA4AA9B7117AC4D2DB61E487C4CBB52D59554C5E20CCB19767C1DCA5212",
          "status":4,
          "version":"0.0.4"
        },
        {
          "account_count":3,
          "balance":"536652083076923070",
          "hash":"A534B0451771A35021BF47E09F0865C3E652B78B8104CDF9944A5709870BF63B",
          "id":"0003",
          "ipv4":"191.123.21.23",
          "message_hash":"70B8290E9DB0DC611CAAA3E38DF11B7D0E1EC41219AB00CF7B7B628644460429",
          "msid":"3",
          "mtim":"2018-07-30T15:00:00+02:00",
          "port":80,
          "public_key":"9D46567A482F8F6AA567804EF2274F6ACEB370D8F7461C3A0DF4CE2C0DF432EB",
          "status":3,
          "version":"0.0.3"
        },
        {
          "account_count":2,
          "balance":"655908101538461530",
          "hash":"70D11F677A9B8F0A49BAD9DCE9F715FCE4AA76BDF23C14FB40142999A1E84577",
          "id":"0002",
          "ipv4":"191.123.21.23",
          "message_hash":"A534B0451771A35021BF47E09F0865C3E652B78B8104CDF9944A5709870BF63B",
          "msid":"2",
          "mtim":"2018-07-30T15:00:00+02:00",
          "port":80,
          "public_key":"3A9F69FEDCB0694123899C1EA7E14256CAD7B4A2C86311FC6B5ED4027404A282",
          "status":2,
          "version":"0.0.2"
        },
        {
          "account_count":1,
          "balance":"655908101538461530",
          "hash":"524769EE119CBCC27A8F8DE7D8A55CA12E9773F4763C40394434F522C43A1463",
          "id":"0001",
          "ipv4":"191.123.21.23",
          "message_hash":"70D11F677A9B8F0A49BAD9DCE9F715FCE4AA76BDF23C14FB40142999A1E84577",
          "msid":"1",
          "mtim":"2018-07-30T15:00:00+02:00",
          "port":80,
          "public_key":"A9C0D972D8AAB73805EC4A28291E052E3B5FAFE0ADC9D724917054E5E2690363",
          "status":1,
          "version":"0.0.1"
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
          "account_count":6,
          "balance":"1886044087591240",
          "hash":"26710F00488043124564798C1D5B617CE54371C6334D54987FF0991A25A5324D",
          "id":"0006",
          "ipv4":"191.123.21.23",
          "message_hash":"524769EE119CBCC27A8F8DE7D8A55CA12E9773F4763C40394434F522C43A1463",
          "msid":"6",
          "mtim":"2018-07-30T15:00:00+02:00",
          "port":80,
          "public_key":"6431A8580B014DA2420FF32842B0BA3CAB3B77F01D1150E5A0D34743F243B778",
          "status":6
        },
        {
          "account_count":5,
          "balance":"417396064615384610",
          "hash":"07187918DEC935E75D00B967B8AC8FF350168ED27A9A6ADFE78A7141B60F0313",
          "id":"0005",
          "ipv4":"191.123.21.23",
          "message_hash":"26710F00488043124564798C1D5B617CE54371C6334D54987FF0991A25A5324D",
          "msid":"5",
          "mtim":"2018-07-30T15:00:00+02:00",
          "port":80,
          "public_key":"4D68B719B7976A1BD38DEB6A88A97AE6258B564B13394490740B00257C8D1550",
          "status":5
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
          "account_count":4,
          "balance":"477024073846153840",
          "hash":"70B8290E9DB0DC611CAAA3E38DF11B7D0E1EC41219AB00CF7B7B628644460429",
          "id":"0004",
          "ipv4":"191.123.21.23",
          "message_hash":"07187918DEC935E75D00B967B8AC8FF350168ED27A9A6ADFE78A7141B60F0313",
          "msid":"4",
          "mtim":"2018-07-30T15:00:00+02:00",
          "port":80,
          "public_key":"6B541CA4AA9B7117AC4D2DB61E487C4CBB52D59554C5E20CCB19767C1DCA5212",
          "status":4,
          "version":"0.0.4"
        },
        {
          "account_count":5,
          "balance":"417396064615384610",
          "hash":"07187918DEC935E75D00B967B8AC8FF350168ED27A9A6ADFE78A7141B60F0313",
          "id":"0005",
          "ipv4":"191.123.21.23",
          "message_hash":"26710F00488043124564798C1D5B617CE54371C6334D54987FF0991A25A5324D",
          "msid":"5",
          "mtim":"2018-07-30T15:00:00+02:00",
          "port":80,
          "public_key":"4D68B719B7976A1BD38DEB6A88A97AE6258B564B13394490740B00257C8D1550",
          "status":5
        }
      ]
    """

  Scenario: List all available nodes with limit, offset and sort by id asc
    Given I want to get the list of "blockexplorer/nodes"
    And I want to limit to 2
    And I want to sort by "balance"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
      [
        {
          "account_count":1,
          "balance":"655908101538461530",
          "hash":"524769EE119CBCC27A8F8DE7D8A55CA12E9773F4763C40394434F522C43A1463",
          "id":"0001",
          "ipv4":"191.123.21.23",
          "message_hash":"70D11F677A9B8F0A49BAD9DCE9F715FCE4AA76BDF23C14FB40142999A1E84577",
          "msid":"1",
          "mtim":"2018-07-30T15:00:00+02:00",
          "port":80,
          "public_key":"A9C0D972D8AAB73805EC4A28291E052E3B5FAFE0ADC9D724917054E5E2690363",
          "status":1,
          "version":"0.0.1"
        },
        {
          "account_count":2,
          "balance":"655908101538461530",
          "hash":"70D11F677A9B8F0A49BAD9DCE9F715FCE4AA76BDF23C14FB40142999A1E84577",
          "id":"0002",
          "ipv4":"191.123.21.23",
          "message_hash":"A534B0451771A35021BF47E09F0865C3E652B78B8104CDF9944A5709870BF63B",
          "msid":"2",
          "mtim":"2018-07-30T15:00:00+02:00",
          "port":80,
          "public_key":"3A9F69FEDCB0694123899C1EA7E14256CAD7B4A2C86311FC6B5ED4027404A282",
          "status":2,
          "version":"0.0.2"
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
          "message": "Sort value `test` is invalid. Only id, accountCount, messageCount, transactionCount, balance, version values are supported."
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
    Given I want to get the resource "blockexplorer/nodes" with id "0005"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
        {
          "account_count":5,
          "balance":"417396064615384610",
          "hash":"07187918DEC935E75D00B967B8AC8FF350168ED27A9A6ADFE78A7141B60F0313",
          "id":"0005",
          "ipv4":"191.123.21.23",
          "message_hash":"26710F00488043124564798C1D5B617CE54371C6334D54987FF0991A25A5324D",
          "msid":"5",
          "mtim":"2018-07-30T15:00:00+02:00",
          "port":80,
          "public_key":"4D68B719B7976A1BD38DEB6A88A97AE6258B564B13394490740B00257C8D1550",
          "status":5
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
  Scenario: Unable to get the resource by invalid id
    Given I want to get the resource "blockexplorer/nodes" with id "0001-00000000-9B6F"
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
    Given I want to get the resource "blockexplorer/nodes" with id "*1"
    When I request resource
    Then the response status code should be 422
    And the response should contain:
    """
        {
          "code": 422,
          "message": "Invalid resource identity"
        }
    """
