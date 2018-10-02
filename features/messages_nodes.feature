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

  Scenario: List messages by node id
    Given I want to get the list of "blockexplorer/nodes/0001/messages"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
    """
      [

      ]
    """

  Scenario: List messages by node id
    Given I want to get the list of "blockexplorer/nodes/0001/messages"
    And I want to limit to 2
    And I want to offset to 2
    When I request resource
    Then the response status code should be 200
    And the response should contain:
   """
     [

     ]
   """

  Scenario: List messages by node id
    Given I want to get the list of "blockexplorer/nodes/0001/messages"
    And I want to sort by "id"
    And I want to order by "asc"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
   """
     [

     ]
   """

  Scenario: List messages by non-existent node
    Given I want to get the list of "blockexplorer/nodes/0001/messages"
    When I request resource
    Then the response status code should be 200
    And the response should contain:
   """
       []
   """

  Scenario: List messages by invalid node id
    Given I want to get the list of "blockexplorer/nodes/000*/messages"
    When I request resource
    Then the response status code should be 422
    And the response should contain:
   """
      {
        "code":422,
        "message":"Invalid resource identity"
      }
   """

  Scenario: List messages by invalid node id
    Given I want to get the list of "blockexplorer/nodes/messages"
    When I request resource
    Then the response status code should be 422
    And the response should contain:
   """
      {
        "code":422,
        "message":"Invalid resource identity"
      }
   """

  Scenario: List messages by invalid node id
    Given I want to get the list of "blockexplorer/nodes//messages"
    When I request resource
    Then the response status code should be 404
    And the response should contain:
   """
      {
        "code":404,
        "message":"No route found for \"GET \/api\/v1\/blockexplorer\/nodes\/\/messages\""
      }
   """
