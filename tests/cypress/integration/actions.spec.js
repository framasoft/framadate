import faker from 'faker';

context('Actions', () => {
  beforeEach(() => {
    cy.visit('http://localhost:8080');
  });

  // https://on.cypress.io/interacting-with-elements

  it('Access the pages from homepage', () => {
    // https://on.cypress.io/type

      console.log(navigator.language);
    cy.contains('header h2', 'Créez vos propres sondages');
    cy.get('.home-choice').first()
        .find('.fa-calendar').click();

    cy.url().should('include', 'create_poll.php?type=date');
      cy.contains('header h2', 'Poll creation (1 of 3)');

    cy.go('back');

      cy.get('.home-choice').eq(1)
          .find('.fa-th-list').click();

      cy.url().should('include', 'create_poll.php?type=autre');
      cy.contains('header h2', 'Poll creation (1 of 3)');

      cy.go('back');

      cy.get('.home-choice').last()
          .find('.fa-search').click();

      cy.url().should('include', 'find_polls.php');

      cy.contains('main h3', 'Polls saved inside this browser');
      cy.contains('.alert.alert-info', 'There are no polls saved inside your browser yet');
      cy.contains('main h3', 'Send my polls by email')
  });

  it('Changes language sucessfully', () => {
      cy.get('header select.form-control').select('Italiano').next().click();
      cy.contains('header h2', 'Crea il tuo sondaggio');
  });

  it('Creates a poll sucessfully', () => {
      //const email = faker.internet.email();
      const name = faker.name.findName();
      const title = faker.lorem.sentence();

      cy.get('.home-choice').first()
          .find('.fa-calendar').click();
      cy.url().should('include', 'create_poll.php?type=date');
      cy.contains('header h2', 'Poll creation (1 of 3)');

      cy.get('input[name=name]').type(name);
      //cy.get('input[name=mail]').type(email);
      cy.get('input[name=title]').type(title);
      //cy.get('#formulaire').submit();
      cy.get('button[name=gotostep2]').click();

      cy.url().should('include', 'create_date_poll.php');

      cy.get('.input-group.date').first().click();
      // cy.wait(1000);
      cy.get('.datepicker.datepicker-dropdown .datepicker-days thead .datepicker-switch').click();
      cy.get('.datepicker.datepicker-dropdown .datepicker-months thead .datepicker-switch').click();
      cy.contains('.datepicker.datepicker-dropdown .datepicker-years span.year', '2020').click();
      cy.contains('.datepicker.datepicker-dropdown .datepicker-months span.month', 'Nov').click();
      cy.contains('.datepicker.datepicker-dropdown .datepicker-days td.day', '12').click();
      cy.get('input[name="days[]"]').first().should('have.value', '2020-11-12');
      cy.get('input[name="days[]"]').eq(1).type('2020-11-13').should('have.value', '2020-11-13');
      cy.get('button[name="choixheures"').click();

      cy.contains('h4', 'List of options');
      cy.contains('.well.summary li:first-child', 'Thursday 12 November 2020');
      cy.contains('.well.summary li:last-child', 'Friday 13 November 2020');

      cy.get('button[name="confirmation"]').click();

      cy.url().should('include', 'adminstuds.php?poll=');
      cy.contains('#title-form h3', title);
      cy.contains('#name-form', name);
      //cy.contains('#email-form', email);

      cy.contains('table.results .bg-primary.month', 'November 2020');
      cy.get('table.results .bg-primary.day').first().contains('Thu 12');
      cy.get('table.results .bg-primary.day').last().contains('Fri 13');
  })
});
