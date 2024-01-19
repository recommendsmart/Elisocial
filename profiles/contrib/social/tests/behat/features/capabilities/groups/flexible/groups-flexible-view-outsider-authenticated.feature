@api @javascript @flexible-groups
Feature: Flexible groups view access for authenticated but unverified users
  Background:
    Given I enable the module "social_group_flexible_group"
    And I disable that the registered users to be verified immediately

  Scenario: As unverified user I can view a public group I'm not a member of
    Given groups with non-anonymous owner:
      | label      | field_group_description | type           | langcode | field_flexible_group_visibility |
      | Test group | Public visibility       | flexible_group | en       | public                          |
    And I am logged in as a user with the authenticated role

    When I am viewing the group "Test group"

    Then I should see "Test group"

#  @todo https://www.drupal.org/project/social/issues/3325012
#  Scenario: As unverified user I can't view a community group
#    Given groups with non-anonymous owner:
#      | label      | field_group_description | type           | langcode | field_flexible_group_visibility |
#      | Test group | Community visibility    | flexible_group | en       | community                       |
#    And I am logged in as a user with the authenticated role
#
#    When I am viewing the group "Test group"
#
#    Then I should not see "Test group"

  Scenario: As unverified user I can't view a secret group
    Given groups with non-anonymous owner:
      | label      | field_group_description | type           | langcode | field_flexible_group_visibility |
      | Test group | Community visibility    | flexible_group | en       | members                       |
    And I am logged in as a user with the authenticated role

    When I am viewing the group "Test group"

    Then I should not see "Test group"

  Scenario: As unverified user I can view a public group I'm not a member of on the groups search
    Given groups with non-anonymous owner:
      | label      | field_group_description | type           | langcode | field_flexible_group_visibility |
      | Test group | Public visibility       | flexible_group | en       | public                          |
    And Search indexes are up to date
    And I am logged in as a user with the authenticated role

    When I search groups for "Test group"

    Then I should see "Test group"

#  @todo https://www.drupal.org/project/social/issues/3325012
#  Scenario: As unverified user I can't view a community group on the groups search
#    Given groups with non-anonymous owner:
#      | label      | field_group_description | type           | langcode | field_flexible_group_visibility |
#      | Test group | Community visibility    | flexible_group | en       | community                       |
#    And Search indexes are up to date
#    And I am logged in as a user with the authenticated role
#
#    When I search groups for "Test group"
#
#    Then I should not see "Test group"

  Scenario: As unverified user I can't view a secret group on the groups search
    Given groups with non-anonymous owner:
      | label      | field_group_description | type           | langcode | field_flexible_group_visibility |
      | Test group | Secret visibility       | flexible_group | en       | members                         |
    And Search indexes are up to date
    And I am logged in as a user with the authenticated role

    When I search groups for "Test group"

    Then I should not see "Test group"

  Scenario: As unverified user I can view a public group I'm not a member of on the groups overview
    Given groups with non-anonymous owner:
      | label      | field_group_description | type           | langcode | field_flexible_group_visibility |
      | Test group | Public visibility       | flexible_group | en       | public                          |
    And I am logged in as a user with the authenticated role

    When I am viewing the groups overview

    Then I should see "Test group"

  Scenario: As unverified user I can't view a community group on the groups overview
    Given groups with non-anonymous owner:
      | label      | field_group_description | type           | langcode | field_flexible_group_visibility |
      | Test group | Community visibility    | flexible_group | en       | community                       |
    And I am logged in as a user with the authenticated role

    When I am viewing the groups overview

    Then I should not see "Test group"

  Scenario: As unverified user I can't view a secret group I'm not a member of on the groups overview
    Given groups with non-anonymous owner:
      | label      | field_group_description | type           | langcode | field_flexible_group_visibility |
      | Test group | Secret visibility       | flexible_group | en       | members                         |
    And I am logged in as a user with the authenticated role

    When I am viewing the groups overview

    Then I should not see "Test group"
