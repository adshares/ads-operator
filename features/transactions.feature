Feature: Transactions
  In order to display accounts in blockexplorer
  As an API client
  I need to be able to fetch single transaction and list of transactions

  Background:
    Given "emptyTransactions" exist in application:
      | id   | blockId | messageId | nodeId | type | size |
      | 0001 | 0001    | 0001      | 0001   | 0    | 0    |

  Scenario: List all available transaction without sort and pagination
    Given I want to get the list of "blockexplorer/emptyTransactions"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
      [
        {
          "address":"0005-00000000-1269",
          "balance":417396064615384610,
          "hash":"07187918DEC935E75D00B967B8AC8FF350168ED27A9A6ADFE78A7141B60F0313",
          "local_change":"2018-07-31T08:49:36+02:00",
         }
      ]
    """

  Scenario: List all available accounts with limit
    Given I want to get the list of "blockexplorer/accounts"
    And I want to limit to 3
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
      [
        {
          "address":"0005-00000000-1269",
          "balance":417396064615384610,
          "hash":"07187918DEC935E75D00B967B8AC8FF350168ED27A9A6ADFE78A7141B60F0313",
          "local_change":"2018-07-31T08:49:36+02:00",
          "msid":5,
          "paired_address":"null",
          "paired_node":5,
          "public_key":"4D68B719B7976A1BD38DEB6A88A97AE6258B564B13394490740B00257C8D1550",
          "remote_change":"2018-07-31T08:51:12+02:00",
          "status":0,
          "time":"2018-07-31T08:49:36+02:00"
        },
        {
          "address":"0004-00000000-B838",
          "balance":477024073846153840,
          "hash":"70B8290E9DB0DC611CAAA3E38DF11B7D0E1EC41219AB00CF7B7B628644460429",
          "local_change":"2018-07-31T08:49:36+02:00",
          "msid":4,
          "paired_address":"null",
          "paired_node":4,
          "public_key":"6B541CA4AA9B7117AC4D2DB61E487C4CBB52D59554C5E20CCB19767C1DCA5212",
          "remote_change":"2018-07-31T08:51:12+02:00",
          "status":0,
          "time":"2018-07-31T08:49:36+02:00"
        },
        {
          "address":"0003-00000000-DFEC",
          "balance":536652083076923070,
          "hash":"A534B0451771A35021BF47E09F0865C3E652B78B8104CDF9944A5709870BF63B",
          "local_change":"2018-07-31T08:49:36+02:00",
          "msid":3,
          "paired_address":"null",
          "paired_node":3,
          "public_key":"9D46567A482F8F6AA567804EF2274F6ACEB370D8F7461C3A0DF4CE2C0DF432EB",
          "remote_change":"2018-07-31T08:51:12+02:00",
          "status":0,
          "time":"2018-07-31T08:49:36+02:00"
        }
      ]
    """

  Scenario: List all available accounts with sort by asc
    Given I want to get the list of "blockexplorer/accounts"
    And I want to limit to 7
    And I want to order by "asc"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
        """
      [
         {
          "address":"0001-00000000-9B6F",
          "balance":655908101538461530,
          "hash":"524769EE119CBCC27A8F8DE7D8A55CA12E9773F4763C40394434F522C43A1463",
          "local_change":"2018-07-31T08:49:36+02:00",
          "msid":1,
          "paired_address":"null",
          "paired_node":1,
          "public_key":"A9C0D972D8AAB73805EC4A28291E052E3B5FAFE0ADC9D724917054E5E2690363",
          "remote_change":"2018-07-31T08:51:12+02:00",
          "status":0,
          "time":"2018-07-31T08:49:36+02:00"
        },
        {
          "address":"0001-00000001-8B4E",
          "balance":1886044087591240,
          "hash":"26710F00488043124564798C1D5B617CE54371C6334D54987FF0991A25A5324D",
          "local_change":"2018-07-31T08:49:36+02:00",
          "msid":6,
          "paired_address":"null",
          "paired_node":1,
          "public_key":"6431A8580B014DA2420FF32842B0BA3CAB3B77F01D1150E5A0D34743F243B778",
          "remote_change":"2018-07-31T08:51:12+02:00",
          "status":0,
          "time":"2018-07-31T08:49:36+02:00"
        },
        {
          "address":"0001-00000002-BB2D",
          "balance":1886044087591240,
          "hash":"BFACA42C051F87BD312D1DDF044D5C18DAAEDF47563214D3C107E688FD5BF29A",
          "local_change":"2018-07-31T08:49:36+02:00",
          "msid":7,
          "paired_address":"null",
          "paired_node":1,
          "public_key":"D2AC1F590F52BF409111E2D7EAF46E2514D8A03ABBEFF0D1CD21DBDF0C25FFE3",
          "remote_change":"2018-07-31T08:51:12+02:00",
          "status":0,
          "time":"2018-07-31T08:49:36+02:00"
        },
        {
          "address":"0001-00000003-AB0C",
          "balance":1886044087591240,
          "hash":"35657662CE38CDE131BD18F1538C1B1D8FC710A108FBFC9D5A00AB88EB9EB041",
          "local_change":"2018-07-31T08:49:36+02:00",
          "msid":8,
          "paired_address":"null",
          "paired_node":1,
          "public_key":"B72283ECE416404D412A7BD175B94973C51E2CA6613ADCB3486D1C1B114D1D90",
          "remote_change":"2018-07-31T08:51:12+02:00",
          "status":0,
          "time":"2018-07-31T08:49:36+02:00"
        },
        {
          "address":"0001-00000004-DBEB",
          "balance":1886044087591240,
          "hash":"5569B007386AB86D9B7760C5D6EF9E60DA1A1378FA2C602345D8E7C88B75129B",
          "local_change":"2018-07-31T08:49:36+02:00",
          "msid":9,
          "paired_address":"null",
          "paired_node":1,
          "public_key":"B281F32AC70BF2508423F531ED13C6446F3378985550BADE83BA31B41A1824A1",
          "remote_change":"2018-07-31T08:51:12+02:00",
          "status":0,
          "time":"2018-07-31T08:49:36+02:00"
        },
        {
          "address":"0001-00000005-CBCA",
          "balance":1886044087591240,
          "hash":"4F91E5E259BB89E012A28508EA180EA93A9E231857CC4E0CB2F2649BB11D3E3D",
          "local_change":"2018-07-31T08:49:36+02:00",
          "msid":10,
          "paired_address":"null",
          "paired_node":1,
          "public_key":"6FAB00CC8AA65FF6C981C8EDDD87469FDF43635CCD7B08C2D48D38EDE0B1D1FF",
          "remote_change":"2018-07-31T08:51:12+02:00",
          "status":0,
          "time":"2018-07-31T08:49:36+02:00"
        },
        {
          "address":"0002-00000000-75BD",
          "balance":596280092307692300,
          "hash":"70D11F677A9B8F0A49BAD9DCE9F715FCE4AA76BDF23C14FB40142999A1E84577",
          "local_change":"2018-07-31T08:49:36+02:00",
          "msid":2,
          "paired_address":"null",
          "paired_node":2,
          "public_key":"3A9F69FEDCB0694123899C1EA7E14256CAD7B4A2C86311FC6B5ED4027404A282",
          "remote_change":"2018-07-31T08:51:12+02:00",
          "status":0,
          "time":"2018-07-31T08:49:36+02:00"
        }
      ]
    """
  Scenario: List all available accounts with sort by id
    Given I want to get the list of "blockexplorer/accounts"
    And I want to limit to 2
    And I want to sort by "id"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
      [
        {
          "address":"0005-00000000-1269",
          "balance":417396064615384610,
          "hash":"07187918DEC935E75D00B967B8AC8FF350168ED27A9A6ADFE78A7141B60F0313",
          "local_change":"2018-07-31T08:49:36+02:00",
          "msid":5,
          "paired_address":"null",
          "paired_node":5,
          "public_key":"4D68B719B7976A1BD38DEB6A88A97AE6258B564B13394490740B00257C8D1550",
          "remote_change":"2018-07-31T08:51:12+02:00",
          "status":0,
          "time":"2018-07-31T08:49:36+02:00"
        },
        {
          "address":"0004-00000000-B838",
          "balance":477024073846153840,
          "hash":"70B8290E9DB0DC611CAAA3E38DF11B7D0E1EC41219AB00CF7B7B628644460429",
          "local_change":"2018-07-31T08:49:36+02:00",
          "msid":4,
          "paired_address":"null",
          "paired_node":4,
          "public_key":"6B541CA4AA9B7117AC4D2DB61E487C4CBB52D59554C5E20CCB19767C1DCA5212",
          "remote_change":"2018-07-31T08:51:12+02:00",
          "status":0,
          "time":"2018-07-31T08:49:36+02:00"
        }
      ]
    """

  Scenario: List all available accounts with sort by asc
    Given I want to get the list of "blockexplorer/accounts"
    And I want to limit to 2
    And I want to order by "asc"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
      [
        {
          "address":"0001-00000000-9B6F",
          "balance":655908101538461530,
          "hash":"524769EE119CBCC27A8F8DE7D8A55CA12E9773F4763C40394434F522C43A1463",
          "local_change":"2018-07-31T08:49:36+02:00",
          "msid":1,
          "paired_address":"null",
          "paired_node":1,
          "public_key":"A9C0D972D8AAB73805EC4A28291E052E3B5FAFE0ADC9D724917054E5E2690363",
          "remote_change":"2018-07-31T08:51:12+02:00",
          "status":0,
          "time":"2018-07-31T08:49:36+02:00"
        },
        {
          "address":"0001-00000001-8B4E",
          "balance":1886044087591240,
          "hash":"26710F00488043124564798C1D5B617CE54371C6334D54987FF0991A25A5324D",
          "local_change":"2018-07-31T08:49:36+02:00",
          "msid":6,
          "paired_address":"null",
          "paired_node":1,
          "public_key":"6431A8580B014DA2420FF32842B0BA3CAB3B77F01D1150E5A0D34743F243B778",
          "remote_change":"2018-07-31T08:51:12+02:00",
          "status":0,
          "time":"2018-07-31T08:49:36+02:00"
        }
      ]
    """

  Scenario: List all available accounts with offset and sort by id asc
    Given I want to get the list of "blockexplorer/accounts"
    And I want to offset to 9
    And I want to sort by "id"
    And I want to order by "asc"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
      [
        {
          "address":"0005-00000000-1269",
          "balance":417396064615384610,
          "hash":"07187918DEC935E75D00B967B8AC8FF350168ED27A9A6ADFE78A7141B60F0313",
          "local_change":"2018-07-31T08:49:36+02:00",
          "msid":5,
          "paired_address":"null",
          "paired_node":5,
          "public_key":"4D68B719B7976A1BD38DEB6A88A97AE6258B564B13394490740B00257C8D1550",
          "remote_change":"2018-07-31T08:51:12+02:00",
          "status":0,
          "time":"2018-07-31T08:49:36+02:00"
        }
      ]
    """

  Scenario: List all available accounts with limit, offset and sort by id asc
    Given I want to get the list of "blockexplorer/accounts"
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
          "address":"0001-00000002-BB2D",
          "balance":1886044087591240,
          "hash":"BFACA42C051F87BD312D1DDF044D5C18DAAEDF47563214D3C107E688FD5BF29A",
          "local_change":"2018-07-31T08:49:36+02:00",
          "msid":7,
          "paired_address":"null",
          "paired_node":1,
          "public_key":"D2AC1F590F52BF409111E2D7EAF46E2514D8A03ABBEFF0D1CD21DBDF0C25FFE3",
          "remote_change":"2018-07-31T08:51:12+02:00",
          "status":0,
          "time":"2018-07-31T08:49:36+02:00"
        },
        {
          "address":"0001-00000003-AB0C",
          "balance":1886044087591240,
          "hash":"35657662CE38CDE131BD18F1538C1B1D8FC710A108FBFC9D5A00AB88EB9EB041",
          "local_change":"2018-07-31T08:49:36+02:00",
          "msid":8,
          "paired_address":"null",
          "paired_node":1,
          "public_key":"B72283ECE416404D412A7BD175B94973C51E2CA6613ADCB3486D1C1B114D1D90",
          "remote_change":"2018-07-31T08:51:12+02:00",
          "status":0,
          "time":"2018-07-31T08:49:36+02:00"
        }
      ]
    """

  Scenario: Unable to get list of accounts with invalid sort field
    Given I want to get the list of "blockexplorer/accounts"
    And I want to sort by "test"
    When I request resource
    Then the response status code should be 400
    And the response should contain:
    """
        {
          "code": 400,
          "message":"Sort value `test` is invalid. Only id, time values are supported."
        }
    """

  Scenario: Unable to get list of accounts with invalid order field
    Given I want to get the list of "blockexplorer/accounts"
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

  Scenario: Unable to get list of accounts with invalid limit field
    Given I want to get the list of "blockexplorer/accounts"
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

  Scenario: Unable to get list of accounts with invalid offset field
    Given I want to get the list of "blockexplorer/accounts"
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

  Scenario: Get single accounts
    Given I want to get the resource "blockexplorer/accounts" with id "0001-00000000-9B6F"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
        {
          "address":"0001-00000000-9B6F",
          "balance":655908101538461530,
          "hash":"524769EE119CBCC27A8F8DE7D8A55CA12E9773F4763C40394434F522C43A1463",
          "local_change":"2018-07-31T08:49:36+02:00",
          "msid":1,
          "paired_address":"null",
          "paired_node":1,
          "public_key":"A9C0D972D8AAB73805EC4A28291E052E3B5FAFE0ADC9D724917054E5E2690363",
          "remote_change":"2018-07-31T08:51:12+02:00",
          "status":0,
          "time":"2018-07-31T08:49:36+02:00"
        }
    """

  Scenario: Unable to get non-existent resource
    Given I want to get the resource "blockexplorer/accounts" with id "0011-00000000-9B6F"
    When I request resource
    Then the response status code should be 404
    And the response should contain:
    """
        {
          "code": 404,
          "message": "The requested resource: 0011-00000000-9B6F was not found"
        }
    """

  Scenario: Unable to get the resource by invalid id
    Given I want to get the resource "blockexplorer/accounts" with id "123-22"
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
    Given I want to get the resource "blockexplorer/accounts" with id "0001*"
    When I request resource
    Then the response status code should be 422
    And the response should contain:
    """
        {
          "code":422,
          "message":"Invalid resource identity"
        }
    """
