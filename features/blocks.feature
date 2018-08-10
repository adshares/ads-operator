Feature: Blocks
  In order to display blocks in blockexplorer
  As an API client
  I need to be able to fetch single block and list of blocks

  Background:
    Given "blocks" exist in application:
      | id       | dividendBalance | dividendPay | messageCount | nodhash                                                          | minhash                                                          | msghash                                                          | nowhash                                                          | oldhash                                                          | viphash                                                          | nodeCount | time                          | voteYes | voteNo | voteTotal | transactionCount |
      | 1B6180E0 | 1               | true        | 2            | 6C2CD43316AAA1E3A8577182A1F06BB85F5EBB9388A584F788151CF08AF204E7 | 6C2CD43316AAA1E3A8577182A1F06BB85F5EBB9388A584F788151CF08AF204E7 | 211B6D3AFBB6B9B452498BB489FBCFC958374C0586A2898E2D76901D87AC6638 | DCBFF04691248935A3E3AE2E8C68DB07CCE6F357157491C2A8C3A01B215D42CA | 9EF5CBE09BA6D0A0D2C43856DCCB03A94DB0A05A740C11E6744A033005D39703 | 2A4831F1459C42E2CCF5C4E202C3301F94C381B6FB253DFED21DD015180D9507 | 3         | 2018-08-01T11:44:00.000+02:00 | 4       | 5      | 6         | 7                |
      | 2B6180E0 | 2               | true        | 3            | 73A5C92FA5142599B1C9863B43E026AFEFA6B57AEE8D189241C7F50C90BA5122 | 73A5C92FA5142599B1C9863B43E026AFEFA6B57AEE8D189241C7F50C90BA5122 | D2AC1F590F52BF409111E2D7EAF46E2514D8A03ABBEFF0D1CD21DBDF0C25FFE3 | BFACA42C051F87BD312D1DDF044D5C18DAAEDF47563214D3C107E688FD5BF29A | 442F8958E2066CB218398D68439ABBB438B6EBC28747D818D7088EFB91ED0020 | AB925153616AC066E5FD6D549CC610AD7DDC6844A8C3DFC5293ED234FA166D05 | 4         | 2018-08-01T11:44:00.000+02:00 | 5       | 6      | 7         | 8                |
      | 3B6180E0 | 3               | true        | 4            | A9C0D972D8AAB73805EC4A28291E052E3B5FAFE0ADC9D724917054E5E2690363 | A9C0D972D8AAB73805EC4A28291E052E3B5FAFE0ADC9D724917054E5E2690363 | B72283ECE416404D412A7BD175B94973C51E2CA6613ADCB3486D1C1B114D1D90 | 35657662CE38CDE131BD18F1538C1B1D8FC710A108FBFC9D5A00AB88EB9EB041 | F8605F33263967A55BB95926943492925B31B1E932604D2DD7EECC9C9CD66FFF | AA40B94D9AF14B331221DCBE7B5CA4F7D20B6055747FA06A65E7522684C9C8FA | 5         | 2018-08-01T11:44:00.000+02:00 | 6       | 7      | 8         | 9                |
      | 4B6180E0 | 4               | true        | 5            | BB3425F914CA9F661CA6F3B908E07092B5AFB7F2FDAE2E94EDE12C83207CA743 | BB3425F914CA9F661CA6F3B908E07092B5AFB7F2FDAE2E94EDE12C83207CA743 | B281F32AC70BF2508423F531ED13C6446F3378985550BADE83BA31B41A1824A1 | 5569B007386AB86D9B7760C5D6EF9E60DA1A1378FA2C602345D8E7C88B75129B | A1A25F101FD730770729591861B2A44086BB6D3E0054E0EFAB9583AD5F752742 | 43611ACF7E5B23074F6AF6BBB086F9C37D671B9CD295A0F59BB42E5528489A90 | 6         | 2018-08-01T11:44:00.000+02:00 | 7       | 8      | 9         | 10               |
      | 5B6180E0 | 5               | true        | 6            | 6431A8580B014DA2420FF32842B0BA3CAB3B77F01D1150E5A0D34743F243B778 | 6431A8580B014DA2420FF32842B0BA3CAB3B77F01D1150E5A0D34743F243B778 | 6FAB00CC8AA65FF6C981C8EDDD87469FDF43635CCD7B08C2D48D38EDE0B1D1FF | 4F91E5E259BB89E012A28508EA180EA93A9E231857CC4E0CB2F2649BB11D3E3D | 24E0A9B923A789C0776DF70F53F202220025E980CA014BF364DA2AFA2DECEE2F | 1D6B16D740508AB7C4A95ECB8D8B35BE8199342E3676912C6757E637D85CA1A4 | 7         | 2018-08-01T11:44:00.000+02:00 | 8       | 9      | 10        | 11               |
      | 6B6180E0 | 6               | true        | 7            | 26710F00488043124564798C1D5B617CE54371C6334D54987FF0991A25A5324D | 26710F00488043124564798C1D5B617CE54371C6334D54987FF0991A25A5324D | EE2D7583E78569F7485FCB8FF4D89525161E5D56447B62C87F8370538DB769E8 | E45A3D41354E33E9FF40A7C6AC39B01A922B8B61BAEEA94B2DC8C4C712B2CF6C | D308B858B75750088C8C1D6D9F5111FA6AA72641E8FF1C0DEDBFC4914AE1BD7B | F88F892A610346D2B8C2E2B22416203A2AFD2076597099B63D36733131045378 | 8         | 2018-08-01T11:44:00.000+02:00 | 9       | 10     | 11        | 12               |

  Scenario: List all available blocks without sort and pagination
    Given I want to get the list of "blockexplorer/blocks"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
      [
        {
          "dividend_balance":600000000000,
          "dividend_pay":true,
          "id":"6B6180E0",
          "message_count":7,
          "min_hash":"26710F00488043124564798C1D5B617CE54371C6334D54987FF0991A25A5324D",
          "msg_hash":"EE2D7583E78569F7485FCB8FF4D89525161E5D56447B62C87F8370538DB769E8",
          "node_count":8,
          "nod_hash":"26710F00488043124564798C1D5B617CE54371C6334D54987FF0991A25A5324D",
          "now_hash":"E45A3D41354E33E9FF40A7C6AC39B01A922B8B61BAEEA94B2DC8C4C712B2CF6C",
          "old_hash":"D308B858B75750088C8C1D6D9F5111FA6AA72641E8FF1C0DEDBFC4914AE1BD7B",
          "time":"2018-08-01T11:44:00+02:00",
          "vip_hash":"F88F892A610346D2B8C2E2B22416203A2AFD2076597099B63D36733131045378",
          "vote_no":10,
          "vote_total":11,
          "vote_yes":9,
          "transaction_count":12
        },
        {
          "dividend_balance":500000000000,
          "dividend_pay":true,
          "id":"5B6180E0",
          "message_count":6,
          "min_hash":"6431A8580B014DA2420FF32842B0BA3CAB3B77F01D1150E5A0D34743F243B778",
          "msg_hash":"6FAB00CC8AA65FF6C981C8EDDD87469FDF43635CCD7B08C2D48D38EDE0B1D1FF",
          "node_count":7,
          "nod_hash":"6431A8580B014DA2420FF32842B0BA3CAB3B77F01D1150E5A0D34743F243B778",
          "now_hash":"4F91E5E259BB89E012A28508EA180EA93A9E231857CC4E0CB2F2649BB11D3E3D",
          "old_hash":"24E0A9B923A789C0776DF70F53F202220025E980CA014BF364DA2AFA2DECEE2F",
          "time":"2018-08-01T11:44:00+02:00",
          "vip_hash":"1D6B16D740508AB7C4A95ECB8D8B35BE8199342E3676912C6757E637D85CA1A4",
          "vote_no":9,
          "vote_total":10,
          "vote_yes":8,
          "transaction_count":11
        },
        {
          "dividend_balance":400000000000,
          "dividend_pay":true,
          "id":"4B6180E0",
          "message_count":5,
          "min_hash":"BB3425F914CA9F661CA6F3B908E07092B5AFB7F2FDAE2E94EDE12C83207CA743",
          "msg_hash":"B281F32AC70BF2508423F531ED13C6446F3378985550BADE83BA31B41A1824A1",
          "node_count":6,
          "nod_hash":"BB3425F914CA9F661CA6F3B908E07092B5AFB7F2FDAE2E94EDE12C83207CA743",
          "now_hash":"5569B007386AB86D9B7760C5D6EF9E60DA1A1378FA2C602345D8E7C88B75129B",
          "old_hash":"A1A25F101FD730770729591861B2A44086BB6D3E0054E0EFAB9583AD5F752742",
          "time":"2018-08-01T11:44:00+02:00",
          "vip_hash":"43611ACF7E5B23074F6AF6BBB086F9C37D671B9CD295A0F59BB42E5528489A90",
          "vote_no":8,
          "vote_total":9,
          "vote_yes":7,
          "transaction_count":10
        },
        {
          "dividend_balance":300000000000,
          "dividend_pay":true,
          "id":"3B6180E0",
          "message_count":4,
          "min_hash":"A9C0D972D8AAB73805EC4A28291E052E3B5FAFE0ADC9D724917054E5E2690363",
          "msg_hash":"B72283ECE416404D412A7BD175B94973C51E2CA6613ADCB3486D1C1B114D1D90",
          "node_count":5,
          "nod_hash":"A9C0D972D8AAB73805EC4A28291E052E3B5FAFE0ADC9D724917054E5E2690363",
          "now_hash":"35657662CE38CDE131BD18F1538C1B1D8FC710A108FBFC9D5A00AB88EB9EB041",
          "old_hash":"F8605F33263967A55BB95926943492925B31B1E932604D2DD7EECC9C9CD66FFF",
          "time":"2018-08-01T11:44:00+02:00",
          "vip_hash":"AA40B94D9AF14B331221DCBE7B5CA4F7D20B6055747FA06A65E7522684C9C8FA",
          "vote_no":7,
          "vote_total":8,
          "vote_yes":6,
          "transaction_count":9
        },
        {
          "dividend_balance":200000000000,
          "dividend_pay":true,
          "id":"2B6180E0",
          "message_count":3,
          "min_hash":"73A5C92FA5142599B1C9863B43E026AFEFA6B57AEE8D189241C7F50C90BA5122",
          "msg_hash":"D2AC1F590F52BF409111E2D7EAF46E2514D8A03ABBEFF0D1CD21DBDF0C25FFE3",
          "node_count":4,
          "nod_hash":"73A5C92FA5142599B1C9863B43E026AFEFA6B57AEE8D189241C7F50C90BA5122",
          "now_hash":"BFACA42C051F87BD312D1DDF044D5C18DAAEDF47563214D3C107E688FD5BF29A",
          "old_hash":"442F8958E2066CB218398D68439ABBB438B6EBC28747D818D7088EFB91ED0020",
          "time":"2018-08-01T11:44:00+02:00",
          "vip_hash":"AB925153616AC066E5FD6D549CC610AD7DDC6844A8C3DFC5293ED234FA166D05",
          "vote_no":6,
          "vote_total":7,
          "vote_yes":5,
          "transaction_count":8
        },
        {
          "dividend_balance":100000000000,
          "dividend_pay":true,
          "id":"1B6180E0",
          "message_count":2,
          "min_hash":"6C2CD43316AAA1E3A8577182A1F06BB85F5EBB9388A584F788151CF08AF204E7",
          "msg_hash":"211B6D3AFBB6B9B452498BB489FBCFC958374C0586A2898E2D76901D87AC6638",
          "node_count":3,
          "nod_hash":"6C2CD43316AAA1E3A8577182A1F06BB85F5EBB9388A584F788151CF08AF204E7",
          "now_hash":"DCBFF04691248935A3E3AE2E8C68DB07CCE6F357157491C2A8C3A01B215D42CA",
          "old_hash":"9EF5CBE09BA6D0A0D2C43856DCCB03A94DB0A05A740C11E6744A033005D39703",
          "time":"2018-08-01T11:44:00+02:00",
          "vip_hash":"2A4831F1459C42E2CCF5C4E202C3301F94C381B6FB253DFED21DD015180D9507",
          "vote_no":5,
          "vote_total":6,
          "vote_yes":4,
          "transaction_count":7
        }
      ]
    """

  Scenario: List all available blocks with limit
    Given I want to get the list of "blockexplorer/blocks"
    And I want to limit to 3
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
      [
        {
          "dividend_balance":600000000000,
          "dividend_pay":true,
          "id":"6B6180E0",
          "message_count":7,
          "min_hash":"26710F00488043124564798C1D5B617CE54371C6334D54987FF0991A25A5324D",
          "msg_hash":"EE2D7583E78569F7485FCB8FF4D89525161E5D56447B62C87F8370538DB769E8",
          "node_count":8,
          "nod_hash":"26710F00488043124564798C1D5B617CE54371C6334D54987FF0991A25A5324D",
          "now_hash":"E45A3D41354E33E9FF40A7C6AC39B01A922B8B61BAEEA94B2DC8C4C712B2CF6C",
          "old_hash":"D308B858B75750088C8C1D6D9F5111FA6AA72641E8FF1C0DEDBFC4914AE1BD7B",
          "time":"2018-08-01T11:44:00+02:00",
          "vip_hash":"F88F892A610346D2B8C2E2B22416203A2AFD2076597099B63D36733131045378",
          "vote_no":10,
          "vote_total":11,
          "vote_yes":9,
          "transaction_count":12
        },
        {
          "dividend_balance":500000000000,
          "dividend_pay":true,
          "id":"5B6180E0",
          "message_count":6,
          "min_hash":"6431A8580B014DA2420FF32842B0BA3CAB3B77F01D1150E5A0D34743F243B778",
          "msg_hash":"6FAB00CC8AA65FF6C981C8EDDD87469FDF43635CCD7B08C2D48D38EDE0B1D1FF",
          "node_count":7,
          "nod_hash":"6431A8580B014DA2420FF32842B0BA3CAB3B77F01D1150E5A0D34743F243B778",
          "now_hash":"4F91E5E259BB89E012A28508EA180EA93A9E231857CC4E0CB2F2649BB11D3E3D",
          "old_hash":"24E0A9B923A789C0776DF70F53F202220025E980CA014BF364DA2AFA2DECEE2F",
          "time":"2018-08-01T11:44:00+02:00",
          "vip_hash":"1D6B16D740508AB7C4A95ECB8D8B35BE8199342E3676912C6757E637D85CA1A4",
          "vote_no":9,
          "vote_total":10,
          "vote_yes":8,
          "transaction_count":11
        },
        {
          "dividend_balance":400000000000,
          "dividend_pay":true,
          "id":"4B6180E0",
          "message_count":5,
          "min_hash":"BB3425F914CA9F661CA6F3B908E07092B5AFB7F2FDAE2E94EDE12C83207CA743",
          "msg_hash":"B281F32AC70BF2508423F531ED13C6446F3378985550BADE83BA31B41A1824A1",
          "node_count":6,
          "nod_hash":"BB3425F914CA9F661CA6F3B908E07092B5AFB7F2FDAE2E94EDE12C83207CA743",
          "now_hash":"5569B007386AB86D9B7760C5D6EF9E60DA1A1378FA2C602345D8E7C88B75129B",
          "old_hash":"A1A25F101FD730770729591861B2A44086BB6D3E0054E0EFAB9583AD5F752742",
          "time":"2018-08-01T11:44:00+02:00",
          "vip_hash":"43611ACF7E5B23074F6AF6BBB086F9C37D671B9CD295A0F59BB42E5528489A90",
          "vote_no":8,
          "vote_total":9,
          "vote_yes":7,
          "transaction_count":10
        }
      ]
    """

  Scenario: List all available blocks with sort by asc
    Given I want to get the list of "blockexplorer/blocks"
    And I want to limit to 3
    And I want to order by "asc"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
        """
      [
          {
            "dividend_balance":100000000000,
            "dividend_pay":true,
            "id":"1B6180E0",
            "message_count":2,
            "min_hash":"6C2CD43316AAA1E3A8577182A1F06BB85F5EBB9388A584F788151CF08AF204E7",
            "msg_hash":"211B6D3AFBB6B9B452498BB489FBCFC958374C0586A2898E2D76901D87AC6638",
            "node_count":3,
            "nod_hash":"6C2CD43316AAA1E3A8577182A1F06BB85F5EBB9388A584F788151CF08AF204E7",
            "now_hash":"DCBFF04691248935A3E3AE2E8C68DB07CCE6F357157491C2A8C3A01B215D42CA",
            "old_hash":"9EF5CBE09BA6D0A0D2C43856DCCB03A94DB0A05A740C11E6744A033005D39703",
            "time":"2018-08-01T11:44:00+02:00",
            "vip_hash":"2A4831F1459C42E2CCF5C4E202C3301F94C381B6FB253DFED21DD015180D9507",
            "vote_no":5,
            "vote_total":6,
            "vote_yes":4,
            "transaction_count":7
           },
           {
            "dividend_balance":200000000000,
            "dividend_pay":true,
            "id":"2B6180E0",
            "message_count":3,
            "min_hash":"73A5C92FA5142599B1C9863B43E026AFEFA6B57AEE8D189241C7F50C90BA5122",
            "msg_hash":"D2AC1F590F52BF409111E2D7EAF46E2514D8A03ABBEFF0D1CD21DBDF0C25FFE3",
            "node_count":4,
            "nod_hash":"73A5C92FA5142599B1C9863B43E026AFEFA6B57AEE8D189241C7F50C90BA5122",
            "now_hash":"BFACA42C051F87BD312D1DDF044D5C18DAAEDF47563214D3C107E688FD5BF29A",
            "old_hash":"442F8958E2066CB218398D68439ABBB438B6EBC28747D818D7088EFB91ED0020",
            "time":"2018-08-01T11:44:00+02:00",
            "vip_hash":"AB925153616AC066E5FD6D549CC610AD7DDC6844A8C3DFC5293ED234FA166D05",
            "vote_no":6,
            "vote_total":7,
            "vote_yes":5,
            "transaction_count":8
           },
           {
            "dividend_balance":300000000000,
            "dividend_pay":true,
            "id":"3B6180E0",
            "message_count":4,
            "min_hash":"A9C0D972D8AAB73805EC4A28291E052E3B5FAFE0ADC9D724917054E5E2690363",
            "msg_hash":"B72283ECE416404D412A7BD175B94973C51E2CA6613ADCB3486D1C1B114D1D90",
            "node_count":5,
            "nod_hash":"A9C0D972D8AAB73805EC4A28291E052E3B5FAFE0ADC9D724917054E5E2690363",
            "now_hash":"35657662CE38CDE131BD18F1538C1B1D8FC710A108FBFC9D5A00AB88EB9EB041",
            "old_hash":"F8605F33263967A55BB95926943492925B31B1E932604D2DD7EECC9C9CD66FFF",
            "time":"2018-08-01T11:44:00+02:00",
            "vip_hash":"AA40B94D9AF14B331221DCBE7B5CA4F7D20B6055747FA06A65E7522684C9C8FA",
            "vote_no":7,
            "vote_total":8,
            "vote_yes":6,
            "transaction_count":9
           }
      ]
    """
  Scenario: List all available blocks with sort by id
    Given I want to get the list of "blockexplorer/blocks"
    And I want to limit to 2
    And I want to sort by "id"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
      [
        {
          "dividend_balance":600000000000,
          "dividend_pay":true,
          "id":"6B6180E0",
          "message_count":7,
          "min_hash":"26710F00488043124564798C1D5B617CE54371C6334D54987FF0991A25A5324D",
          "msg_hash":"EE2D7583E78569F7485FCB8FF4D89525161E5D56447B62C87F8370538DB769E8",
          "node_count":8,
          "nod_hash":"26710F00488043124564798C1D5B617CE54371C6334D54987FF0991A25A5324D",
          "now_hash":"E45A3D41354E33E9FF40A7C6AC39B01A922B8B61BAEEA94B2DC8C4C712B2CF6C",
          "old_hash":"D308B858B75750088C8C1D6D9F5111FA6AA72641E8FF1C0DEDBFC4914AE1BD7B",
          "time":"2018-08-01T11:44:00+02:00",
          "vip_hash":"F88F892A610346D2B8C2E2B22416203A2AFD2076597099B63D36733131045378",
          "vote_no":10,
          "vote_total":11,
          "vote_yes":9,
          "transaction_count":12
         },
         {
          "dividend_balance":500000000000,
          "dividend_pay":true,
          "id":"5B6180E0",
          "message_count":6,
          "min_hash":"6431A8580B014DA2420FF32842B0BA3CAB3B77F01D1150E5A0D34743F243B778",
          "msg_hash":"6FAB00CC8AA65FF6C981C8EDDD87469FDF43635CCD7B08C2D48D38EDE0B1D1FF",
          "node_count":7,
          "nod_hash":"6431A8580B014DA2420FF32842B0BA3CAB3B77F01D1150E5A0D34743F243B778",
          "now_hash":"4F91E5E259BB89E012A28508EA180EA93A9E231857CC4E0CB2F2649BB11D3E3D",
          "old_hash":"24E0A9B923A789C0776DF70F53F202220025E980CA014BF364DA2AFA2DECEE2F",
          "time":"2018-08-01T11:44:00+02:00",
          "vip_hash":"1D6B16D740508AB7C4A95ECB8D8B35BE8199342E3676912C6757E637D85CA1A4",
          "vote_no":9,
          "vote_total":10,
          "vote_yes":8,
          "transaction_count":11
         }
      ]
    """

  Scenario: List all available blocks with sort by asc
    Given I want to get the list of "blockexplorer/blocks"
    And I want to limit to 2
    And I want to order by "asc"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
      [
        {
          "dividend_balance":100000000000,
          "dividend_pay":true,
          "id":"1B6180E0",
          "message_count":2,
          "min_hash":"6C2CD43316AAA1E3A8577182A1F06BB85F5EBB9388A584F788151CF08AF204E7",
          "msg_hash":"211B6D3AFBB6B9B452498BB489FBCFC958374C0586A2898E2D76901D87AC6638",
          "node_count":3,
          "nod_hash":"6C2CD43316AAA1E3A8577182A1F06BB85F5EBB9388A584F788151CF08AF204E7",
          "now_hash":"DCBFF04691248935A3E3AE2E8C68DB07CCE6F357157491C2A8C3A01B215D42CA",
          "old_hash":"9EF5CBE09BA6D0A0D2C43856DCCB03A94DB0A05A740C11E6744A033005D39703",
          "time":"2018-08-01T11:44:00+02:00",
          "vip_hash":"2A4831F1459C42E2CCF5C4E202C3301F94C381B6FB253DFED21DD015180D9507",
          "vote_no":5,
          "vote_total":6,
          "vote_yes":4,
          "transaction_count":7
         },
         {
          "dividend_balance":200000000000,
          "dividend_pay":true,
          "id":"2B6180E0",
          "message_count":3,
          "min_hash":"73A5C92FA5142599B1C9863B43E026AFEFA6B57AEE8D189241C7F50C90BA5122",
          "msg_hash":"D2AC1F590F52BF409111E2D7EAF46E2514D8A03ABBEFF0D1CD21DBDF0C25FFE3",
          "node_count":4,
          "nod_hash":"73A5C92FA5142599B1C9863B43E026AFEFA6B57AEE8D189241C7F50C90BA5122",
          "now_hash":"BFACA42C051F87BD312D1DDF044D5C18DAAEDF47563214D3C107E688FD5BF29A",
          "old_hash":"442F8958E2066CB218398D68439ABBB438B6EBC28747D818D7088EFB91ED0020",
          "time":"2018-08-01T11:44:00+02:00",
          "vip_hash":"AB925153616AC066E5FD6D549CC610AD7DDC6844A8C3DFC5293ED234FA166D05",
          "vote_no":6,
          "vote_total":7,
          "vote_yes":5,
          "transaction_count":8
         }
      ]
    """

  Scenario: List all available blocks with offset and sort by id asc
    Given I want to get the list of "blockexplorer/blocks"
    And I want to offset to 4
    And I want to sort by "id"
    And I want to order by "asc"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
      [
        {
          "dividend_balance":500000000000,
          "dividend_pay":true,
          "id":"5B6180E0",
          "message_count":6,
          "min_hash":"6431A8580B014DA2420FF32842B0BA3CAB3B77F01D1150E5A0D34743F243B778",
          "msg_hash":"6FAB00CC8AA65FF6C981C8EDDD87469FDF43635CCD7B08C2D48D38EDE0B1D1FF",
          "node_count":7,
          "nod_hash":"6431A8580B014DA2420FF32842B0BA3CAB3B77F01D1150E5A0D34743F243B778",
          "now_hash":"4F91E5E259BB89E012A28508EA180EA93A9E231857CC4E0CB2F2649BB11D3E3D",
          "old_hash":"24E0A9B923A789C0776DF70F53F202220025E980CA014BF364DA2AFA2DECEE2F",
          "time":"2018-08-01T11:44:00+02:00",
          "vip_hash":"1D6B16D740508AB7C4A95ECB8D8B35BE8199342E3676912C6757E637D85CA1A4",
          "vote_no":9,
          "vote_total":10,
          "vote_yes":8,
          "transaction_count":11
        },
        {
          "dividend_balance":600000000000,
          "dividend_pay":true,
          "id":"6B6180E0",
          "message_count":7,
          "min_hash":"26710F00488043124564798C1D5B617CE54371C6334D54987FF0991A25A5324D",
          "msg_hash":"EE2D7583E78569F7485FCB8FF4D89525161E5D56447B62C87F8370538DB769E8",
          "node_count":8,
          "nod_hash":"26710F00488043124564798C1D5B617CE54371C6334D54987FF0991A25A5324D",
          "now_hash":"E45A3D41354E33E9FF40A7C6AC39B01A922B8B61BAEEA94B2DC8C4C712B2CF6C",
          "old_hash":"D308B858B75750088C8C1D6D9F5111FA6AA72641E8FF1C0DEDBFC4914AE1BD7B",
          "time":"2018-08-01T11:44:00+02:00",
          "vip_hash":"F88F892A610346D2B8C2E2B22416203A2AFD2076597099B63D36733131045378",
          "vote_no":10,
          "vote_total":11,
          "vote_yes":9,
          "transaction_count":12
        }
      ]
    """

  Scenario: List all available blocks with limit, offset and sort by id asc
    Given I want to get the list of "blockexplorer/blocks"
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
          "dividend_balance":300000000000,
          "dividend_pay":true,
          "id":"3B6180E0",
          "message_count":4,
          "min_hash":"A9C0D972D8AAB73805EC4A28291E052E3B5FAFE0ADC9D724917054E5E2690363",
          "msg_hash":"B72283ECE416404D412A7BD175B94973C51E2CA6613ADCB3486D1C1B114D1D90",
          "node_count":5,
          "nod_hash":"A9C0D972D8AAB73805EC4A28291E052E3B5FAFE0ADC9D724917054E5E2690363",
          "now_hash":"35657662CE38CDE131BD18F1538C1B1D8FC710A108FBFC9D5A00AB88EB9EB041",
          "old_hash":"F8605F33263967A55BB95926943492925B31B1E932604D2DD7EECC9C9CD66FFF",
          "time":"2018-08-01T11:44:00+02:00",
          "vip_hash":"AA40B94D9AF14B331221DCBE7B5CA4F7D20B6055747FA06A65E7522684C9C8FA",
          "vote_no":7,
          "vote_total":8,
          "vote_yes":6,
          "transaction_count":9
        },
        {
          "dividend_balance":400000000000,
          "dividend_pay":true,
          "id":"4B6180E0",
          "message_count":5,
          "min_hash":"BB3425F914CA9F661CA6F3B908E07092B5AFB7F2FDAE2E94EDE12C83207CA743",
          "msg_hash":"B281F32AC70BF2508423F531ED13C6446F3378985550BADE83BA31B41A1824A1",
          "node_count":6,
          "nod_hash":"BB3425F914CA9F661CA6F3B908E07092B5AFB7F2FDAE2E94EDE12C83207CA743",
          "now_hash":"5569B007386AB86D9B7760C5D6EF9E60DA1A1378FA2C602345D8E7C88B75129B",
          "old_hash":"A1A25F101FD730770729591861B2A44086BB6D3E0054E0EFAB9583AD5F752742",
          "time":"2018-08-01T11:44:00+02:00",
          "vip_hash":"43611ACF7E5B23074F6AF6BBB086F9C37D671B9CD295A0F59BB42E5528489A90",
          "vote_no":8,
          "vote_total":9,
          "vote_yes":7,
          "transaction_count":10
        }
      ]
    """

  Scenario: Unable to get list of blocks with invalid sort field
    Given I want to get the list of "blockexplorer/blocks"
    And I want to sort by "test"
    When I request resource
    Then the response status code should be 400
    And the response should contain:
    """
        {
          "code":400,
          "message":"Sort value `test` is invalid. Only id, time values are supported."
        }
    """

  Scenario: Unable to get list of blocks with invalid order field
    Given I want to get the list of "blockexplorer/blocks"
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

  Scenario: Unable to get list of blocks with invalid limit field
    Given I want to get the list of "blockexplorer/blocks"
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

  Scenario: Unable to get list of blocks with invalid offset field
    Given I want to get the list of "blockexplorer/blocks"
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

  Scenario: Get single blocks
    Given I want to get the resource "blockexplorer/blocks" with id "1B6180E0"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
        {
          "dividend_balance":100000000000,
          "dividend_pay":true,
          "id":"1B6180E0",
          "message_count":2,
          "min_hash":"6C2CD43316AAA1E3A8577182A1F06BB85F5EBB9388A584F788151CF08AF204E7",
          "msg_hash":"211B6D3AFBB6B9B452498BB489FBCFC958374C0586A2898E2D76901D87AC6638",
          "node_count":3,
          "nod_hash":"6C2CD43316AAA1E3A8577182A1F06BB85F5EBB9388A584F788151CF08AF204E7",
          "now_hash":"DCBFF04691248935A3E3AE2E8C68DB07CCE6F357157491C2A8C3A01B215D42CA",
          "old_hash":"9EF5CBE09BA6D0A0D2C43856DCCB03A94DB0A05A740C11E6744A033005D39703",
          "time":"2018-08-01T11:44:00+02:00",
          "vip_hash":"2A4831F1459C42E2CCF5C4E202C3301F94C381B6FB253DFED21DD015180D9507",
          "vote_no":5,
          "vote_total":6,
          "vote_yes":4,
          "transaction_count":7
        }
    """

  Scenario: Unable to get non-existent resource
    Given I want to get the resource "blockexplorer/blocks" with id "9B6180E0"
    When I request resource
    Then the response status code should be 404
    And the response should contain:
    """
        {
          "code": 404,
          "message": "The requested resource: 9B6180E0 was not found"
        }
    """

  Scenario: Unable to get the resource by invalid id
    Given I want to get the resource "blockexplorer/blocks" with id "123-22"
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
    Given I want to get the resource "blockexplorer/blocks" with id "0001*"
    When I request resource
    Then the response status code should be 422
    And the response should contain:
    """
        {
          "code":422,
          "message":"Invalid resource identity"
        }
    """
    
