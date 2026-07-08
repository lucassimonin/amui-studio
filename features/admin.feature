Feature: Administration
  In order to manage the website content
  As an agency client
  I need a secured and usable admin panel

  Scenario: Anonymous visitors cannot access the admin
    When I go to "/admin"
    Then I should be on "/admin/login"

  Scenario: An admin can log in and see the dashboard
    Given I am authenticated as admin
    When I go to "/admin"
    Then the response status code should be 200
    And I should see "Tableau de bord"

  Scenario: The page builder lists the amuï blocks
    Given I am authenticated as admin
    When I go to "/admin/pages"
    And I follow "amuï studio — Portfolio"
    Then the response status code should be 200
    And I should see "Ajouter un bloc"
    And I should see "Hero deux colonnes"
    And I should see "Grille de projets"

  Scenario: An admin can create a redirect
    Given I am authenticated as admin
    When I go to "/admin/redirects"
    And I fill in "source" with "/ancienne-url"
    And I fill in "target" with "/"
    And I press "Créer"
    Then I should see "Redirection créée."
