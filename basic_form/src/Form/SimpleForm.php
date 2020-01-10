<?php

namespace Drupal\basic_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * SimpleForm controller (Extends from FormBase).
 *
 * This form asks ...
 *  - Person's name
 *  - Age
 *  - Gender
 *  - Birthdate
 *  (All required fields)
 *
 *  This form has "Submit" button.
 *
 *  After click the submit button, This form shows the entered values 
 *  by using the Messanger service.
 *
 *  Created by Wills Jan.08.2020
 *
 * @see \Drupal\Core\Form\FormBase
 */
class SimpleForm extends FormBase {

  /**
   * Build the SimpleForm.
   *
   * A build form method constructs an array that defines how markup and
   * other form elements are included in an HTML.
   *
   * @param array $form
   *   Default form array structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object containing current form state.
   *
   * @return array
   *   The render array defining the elements of the form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    //Description
    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Please input Name, Age, Gender, Birthdate and click Submit button'),
    ];

    //Person's Name (Textfield)
    $form['person_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Person\'s Name'),
      '#description' => $this->t('Enter Person\'s name.'),
      '#default_value' => $form_state->getValue('person_name', ''),
      '#required' => TRUE,
    ];


     //Age (Number)
     $form['age'] = [
      '#type' => 'number',
      '#title' => $this->t('Age'),
      '#default_value' => $form_state->getValue('age', ''),
      '#description' => $this->t('Digit only and value between 0 and 150'),
      '#required' => TRUE,
    ];

     //Gender (Select)
     $form['gender'] = [
      '#type' => 'select',
      '#title' => $this->t('Gender'),
      '#options' => [
        'Male' => $this->t('Male'),
        'Female' => $this->t('Female'),
        'Not specified' => $this->t('Not specified'),
      ],
      '#empty_option' => $this->t('-select-'),
      '#description' => $this->t('Select from Male, Female or Not specified'),
      '#required' => TRUE,
    ];


     //Birthdate (Date)
    $form['date_birth'] = [
      '#type' => 'date',
      '#title' => $this->t('Birthdate'),
      '#description' => 'Please select the birthday from calendar',
      '#required' => TRUE,
    ];


    // Submit handlers in an actions element.
    $form['actions'] = [
      '#type' => 'actions',
    ];

    // Add a submit button.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    //return created form array.
    return $form;
  }

  /**
   * Getter method for Form ID.
   *
   * The form ID is used in implementations of hook_form_alter() to allow other
   * modules to alter the render array built by this form controller. It must be
   * unique site wide. It normally starts with the providing module's name.
   *
   * @return string
   *   The unique ID of the form defined by this class.
   */
  public function getFormId() {

    //This form ID
    return 'basic_form_simple_form';

  }

  /**
   * Form validation.
   *
   * @param array $form
   *   The render array of the currently built form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object describing the current state of the form.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    //Check the age and birthday.
    //Get age value.
    $age = $form_state->getValue('age');

    //Get birthday value.
    $date_birth = $form_state->getValue('date_birth');

    //convert from date to time
    $tm1=strtotime($date_birth);

    //Get Year, Month, Date from the birthday.
    $y1=date("Y",$tm1);
    $m1=date("n",$tm1);
    $d1=date("d",$tm1);

    //Get Current Year.
    $y2=date("Y");

    //Calculate the age from current year and birthday's year.
    $calc_age=$y2-$y1;
    $tm_this_year_birth=mktime(0,0,0,$m1,$d1,$y2);

    //Before birthdate.
    if ($tm_this_year_birth > time()){
	    	$calc_age--; //decrease
    }

    //Check the age value 
    if ($age!=$calc_age){
	$form_state->setErrorByName('age', $this->t('The age and birthdate does not match. Calculated age from the date of birth is %calc_age', ['%calc_age' => $calc_age]));
    }

    //Check the age range
    if ($age < 0 || $age > 150){
    	$form_state->setErrorByName('age', $this->t('The age must be between 0 and 150'));
    }

  }

  /**
   * Form submit handler.
   *
   * The submitForm method is the default method called for any submit elements.
   *
   * @param array $form
   *   The render array of the currently built form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object describing the current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    //Show the entered values using the Messenger service
    //Get values (name, gender, age, birthday)
    $person_name = $form_state->getValue('person_name');
    $gender = $form_state->getValue('gender');
    $age = $form_state->getValue('age');
    $date_birth = $form_state->getValue('date_birth');

    //Show the entered values by using Messenger service
    $this->messenger()->addMessage($this->t('You entered values are below.'));
    $this->messenger()->addMessage($this->t('Person\'s Name: %person_name.', ['%person_name' => $person_name]));
    $this->messenger()->addMessage($this->t('Gender: %gender.', ['%gender' => $gender]));
    $this->messenger()->addMessage($this->t('Age: %age.', ['%age' => $age]));
    $this->messenger()->addMessage($this->t('Birth Date: %date_birth.', ['%date_birth' => $date_birth]));

  }

}

?>
