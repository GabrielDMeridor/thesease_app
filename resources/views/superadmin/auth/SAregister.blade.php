@extends('guest-layout')

@section('content-header1')
<title>ThesEase - Register
</title>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card card-container">
                    <img class="card-img-top" src="{{ asset('img/ps.png') }}" style="width:auto;height:200px;">
                    <div class="centered">Register</div>
                </div>
                <div class="card-body">
                    <!-- Display Error Messages -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Display Success Messages -->
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Registration Form -->
                    <form id="registrationForm" action="{{ route('postSARegister') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-floating mb-3">
                            <label for="name" class="entries">Name</label>
                            <input class="form-control @error('name') is-invalid @enderror" id="name" type="text" name="name" value="{{ old('name') }}" required/>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <label for="email" class="entries">Email address</label>
                            <input class="form-control @error('email') is-invalid @enderror" id="email" type="email" name="email" value="{{ old('email') }}" required/>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Password field with eye icon -->
                        <div class="form-floating mb-3">
                            <label for="password" class="entries">Password</label>
                            <div class="input-group">
                                <input class="form-control @error('password') is-invalid @enderror" id="password" type="password" name="password" required/>
                                <span class="input-group-text" style="cursor: pointer;">
                                    <i class="fas fa-eye" id="togglePassword"></i>
                                </span>
                            </div>
                            @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Confirm Password field with eye icon -->
                        <div class="form-floating mb-3">
                            <label for="password_confirmation" class="entries">Confirm Password</label>
                            <div class="input-group">
                                <input class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" type="password" name="password_confirmation" required/>
                                <span class="input-group-text" style="cursor: pointer;">
                                    <i class="fas fa-eye" id="toggleConfirmPassword"></i>
                                </span>
                            </div>
                            @error('password_confirmation')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Account Type Dropdown -->
                        <div class="form-floating mb-3">
                            <label for="account_type" class="entries">Account Type</label>
                            <select class="form-control @error('account_type') is-invalid @enderror" id="account_type" name="account_type" required>
                                <option value="">Select Account Type</option>
                                <option value="12" {{ old('account_type') == 12 ? 'selected' : '' }}>Christian Praxis</option>
                                <option value="6" {{ old('account_type') == 6 ? 'selected' : '' }}>AUF Ethics Review Committee</option>
                                <option value="7" {{ old('account_type') == 7 ? 'selected' : '' }}>Statistician</option>
                                <option value="8" {{ old('account_type') == 8 ? 'selected' : '' }}>OVPRI</option>
                                <option value="9" {{ old('account_type') == 9 ? 'selected' : '' }}>Library</option>
                                <option value="10" {{ old('account_type') == 10 ? 'selected' : '' }}>Language Editor</option>
                                <option value="4" {{ old('account_type') == 4 ? 'selected' : '' }}>Program Chair</option>
                                <option value="5" {{ old('account_type') == 5 ? 'selected' : '' }}>Thesis/Dissertation Professor</option>
                                <option value="11" {{ old('account_type') == 11 ? 'selected' : '' }}>Graduate School Student</option>

                            </select>
                            @error('account_type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Degree Dropdown (only for account type 11) -->
                        <div class="form-floating mb-3" id="degree-container" style="display: none;">
                            <label for="degree" class="entries">Degree</label>
                            <select class="form-control @error('degree') is-invalid @enderror" id="degree" name="degree">
                                <option value="">Select Degree</option>
                                <option value="Masteral" {{ old('degree') == 'Masteral' ? 'selected' : '' }}>Masteral</option>
                                <option value="Doctorate" {{ old('degree') == 'Doctorate' ? 'selected' : '' }}>Doctorate</option>
                            </select>
                            @error('degree')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Program Dropdown (only for account type 11) -->
                        <div class="form-floating mb-3" id="program-container" style="display: none;">
                            <label for="program" class="entries">Program</label>
                            <select class="form-control @error('program') is-invalid @enderror" id="program" name="program">
                                <!-- Options will be populated by JavaScript based on the selected degree -->
                            </select>
                            @error('program')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Nationality Dropdown (only for account type 11) -->
                        <div class="form-floating mb-3" id="nationality-container" style="display: none;">
                            <label for="nationality" class="entries">Nationality</label>
                            <select class="form-control" id="nationality" name="nationality">
                                <option value="">Select Nationality</option>
                                <!-- Options will be populated by JavaScript -->
                            </select>
                        </div>

                        <!-- Immigration Card Upload (only when nationality is not 'Filipino') -->
                        <div class="form-floating mb-3" id="immigration-container" style="display: none;">
                            <label for="immigration_or_studentvisa" class="entries">Upload Immigration Card/Student Visa</label>
                            <input class="form-control" id="immigration_or_studentvisa" type="file" name="immigration_or_studentvisa" accept=".jpg,.jpeg,.png"/>
                        </div>


                        <!-- Manuscript Upload (only for account type 11) -->
                        <div class="form-floating mb-3" id="manuscript-container" style="display: none;">
                            <label for="manuscript" class="entries">Upload Manuscript</label>
                            <input class="form-control" id="manuscript" type="file" name="manuscript" accept=".pdf"/>
                        </div>




                        <hr class="lineborder">

                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                            <a href="{{ route('getSALogin') }}" class="btn btn-secondary ml-2 btn-primary btn-frgt">Go Back</a>
                            <!-- Button to trigger terms modal -->
                            <button type="button" class="btn btn-primary" id="showTermsModal">
                                Register
                            </button>
                        </div>

                        <!-- Terms and Conditions Modal -->
                        <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <!-- OR Modal Header with Font Awesome "X" -->
                                    <div class="modal-header">
                                    <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true"><i class="fas fa-times"></i></span> <!-- Font Awesome "X" -->
                                    </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>
                                            By registering, you agree that your data will be stored and processed solely for academic purposes in compliance with data protection regulations.
                                        </p>
                                        <p>
                                            Your information will not be shared with third parties outside of academic use without your explicit consent.
                                        </p>
                                        <p>
                                            Please read the full terms and conditions to understand how we use and protect your personal data.
                                        </p>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="agreeCheckbox">
                                            <label class="form-check-label" for="agreeCheckbox">I agree to the terms and conditions</label>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" id="acceptTerms">Accept</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    

<!-- Script to toggle password visibility -->
<script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    togglePassword.addEventListener('click', function () {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.classList.toggle('fa-eye-slash');
    });

    const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
    const confirmPassword = document.querySelector('#password_confirmation');
    toggleConfirmPassword.addEventListener('click', function () {
        const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPassword.setAttribute('type', type);
        this.classList.toggle('fa-eye-slash');
    });
            // Show terms modal on clicking Register
            document.getElementById('showTermsModal').addEventListener('click', function (e) {
            e.preventDefault();
            const form = document.getElementById('registrationForm');
            
            // First validate the form without submitting
            if (form.checkValidity()) {
                new bootstrap.Modal(document.getElementById('termsModal')).show();
            } else {
                form.reportValidity();
            }
        });

        // Handle modal "Accept" button click
        document.getElementById('acceptTerms').addEventListener('click', function () {
            const checkbox = document.getElementById('agreeCheckbox');

            if (checkbox.checked) {
                // Submit the form when checkbox is checked
                document.getElementById('registrationForm').submit();
            } else {
                // Show an alert if the checkbox isn't checked
                alert('You must agree to the terms and conditions to proceed.');
            }
        });


    // Define programs for Masteral and Doctorate degrees
    const programs = {
        'Masteral': ['MAEd', 'MA-Psych-CP', 'MBA', 'MS-CJ-Crim', 'MDS', 'MIT', 'MSPH', 'MPH', 'MS-MLS', 'MAN', 'MN'],
        'Doctorate': ['PhD-CI-ELT', 'PhD-Ed-EM', 'PhD-Mgmt', 'DBA', 'DIT', 'DRPH-HPE']
    };
        // Initialize form fields based on old values
        window.addEventListener('DOMContentLoaded', function () {
        if (accountType.value == '11' || accountType.value == '5' || accountType.value == '4') {
            degreeContainer.style.display = 'block';
        }

        if (degree.value) {
            handleDegreeChange();
        }
    });


    // Get HTML elements
    const accountType = document.querySelector('#account_type');
    const degreeContainer = document.querySelector('#degree-container');
    const programContainer = document.querySelector('#program-container');
    const nationalityContainer = document.querySelector('#nationality-container');
    const immigrationContainer = document.querySelector('#immigration-container');
    const routingFormContainer = document.querySelector('#routing-form-container');
    const manuscriptContainer = document.querySelector('#manuscript-container');
    const adviserAppointmentFormContainer = document.querySelector('#adviser-appointment-form-container');
    const nationalityDropdown = document.querySelector('#nationality');
    const degreeLabel = document.querySelector('label[for="degree"]'); // Degree label element
    const degree = document.querySelector('#degree'); // Degree select element
    const programSelect = document.querySelector('#program'); // Program select element

    // Function to reset Degree and Program fields
    function resetDegreeAndProgram() {
        // Reset Degree field
        degree.value = '';
        // Reset Program field
        programSelect.innerHTML = ''; // Clear the program options
        programContainer.style.display = 'none'; // Hide program field by default
    }

    // Function to handle the degree change event
    function handleDegreeChange() {
        const selectedDegree = degree.value;
        if (selectedDegree && programs[selectedDegree]) {
            programContainer.style.display = 'block'; // Ensure Program field is displayed
            programSelect.innerHTML = ''; // Clear previous program options
            programs[selectedDegree].forEach(function (program) {
                const option = document.createElement('option');
                option.value = program;
                option.textContent = program;
                programSelect.appendChild(option);
            });
        } else {
            programContainer.style.display = 'none'; // Hide program if no valid degree selected
        }
    }

    // Handle account type change event
    accountType.addEventListener('change', function () {
        resetDegreeAndProgram(); // Reset Degree and Program whenever the account type changes

        if (accountType.value == '11') {
            // For Graduate School Student (Account Type 11)
            degreeContainer.style.display = 'block';
            nationalityContainer.style.display = 'block';
            manuscriptContainer.style.display = 'block';
            degreeLabel.textContent = 'Degree'; // Set label to 'Degree'
            loadNationalities(); // Load nationalities only when account_type is 11
        } else if (accountType.value == '5' || accountType.value == '4') {
            // For Thesis/Dissertation Professor (Account Type 5) and Program Chair (Account Type 4)
            degreeContainer.style.display = 'block';
            nationalityContainer.style.display = 'none';
            manuscriptContainer.style.display = 'none';
            immigrationContainer.style.display = 'none';
            degreeLabel.textContent = accountType.value == '5' ? 'What degree are you teaching?' : 'Degree'; // Set custom label
        } else {
            // Hide all fields for other account types
            degreeContainer.style.display = 'none';
            programContainer.style.display = 'none';
            nationalityContainer.style.display = 'none';
            manuscriptContainer.style.display = 'none';
            immigrationContainer.style.display = 'none';
        }
    });

    window.addEventListener('DOMContentLoaded', function () {
    // Check if there are validation errors from Laravel
    const hasErrors = @json($errors->any());

    // Get the degree dropdown element
    const degreeDropdown = document.querySelector('#degree');

    if (hasErrors) {
        // Reset the degree dropdown to default (Select Degree) when there is an error
        degreeDropdown.value = ''; // Reset to "Select Degree"
        
        // Also reset the program options because the program depends on the degree
        programSelect.innerHTML = ''; // Clear program options
        programContainer.style.display = 'none'; // Hide the program dropdown
    }
});

    // Ensure proper fields are displayed on page load
    window.addEventListener('DOMContentLoaded', function () {
        if (accountType.value == '11') {
            degreeContainer.style.display = 'block';
            nationalityContainer.style.display = 'block';
            manuscriptContainer.style.display = 'block';
            loadNationalities();
        } else if (accountType.value == '5' || accountType.value == '4') {
            degreeContainer.style.display = 'block';
            degreeLabel.textContent = accountType.value == '5' ? 'What degree are you teaching?' : 'Degree'; // Set custom label
        }
    });

    // Handle degree change event to show program dropdown based on selected degree (for account types 5 and 11, and now 4)
    degree.addEventListener('change', handleDegreeChange);

    // Handle nationality change event

    document.addEventListener('DOMContentLoaded', function () {
    const accountType = document.querySelector('#account_type');
    const nationalityDropdown = document.querySelector('#nationality');
    const immigrationContainer = document.querySelector('#immigration-container');
    const immigrationInput = document.querySelector('#immigration_or_studentvisa');

    // Function to update immigration field based on account type and nationality
    function updateImmigrationField() {
        const nationality = nationalityDropdown.value.toLowerCase();
        const isGraduateStudent = accountType.value === '11';

        if (isGraduateStudent && nationality !== 'filipino') {
            // Show immigration field if Graduate School Student with a foreign nationality
            immigrationContainer.style.display = 'block';
            immigrationInput.setAttribute('required', 'required');
        } else {
            // Hide immigration field otherwise
            immigrationContainer.style.display = 'none';
            immigrationInput.removeAttribute('required');
        }
    }

    // Initialize the visibility of the immigration field on page load
    updateImmigrationField();

    // Update immigration field visibility whenever account type changes
    accountType.addEventListener('change', function () {
        updateImmigrationField();
    });

    // Update immigration field visibility whenever nationality changes
    nationalityDropdown.addEventListener('change', function () {
        updateImmigrationField();
    });
});

    // Load nationalities from API
    async function loadNationalities() {
        try {
            const response = await fetch('https://restcountries.com/v3.1/all');
            const countries = await response.json();
            let nationalities = [];

            countries.forEach(country => {
                if (country.demonyms && country.demonyms.eng) {
                    const nationality = country.demonyms.eng.m;
                    nationalities.push(nationality);
                }
            });

            nationalities.sort();
            nationalities.forEach(nationality => {
                const option = document.createElement('option');
                option.value = nationality;
                option.textContent = nationality;
                nationalityDropdown.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading nationalities:', error);
        }
    }
    

    // Ensure proper fields are displayed on page load
    window.addEventListener('DOMContentLoaded', function () {
        if (accountType.value == '11') {
            degreeContainer.style.display = 'block';
            nationalityContainer.style.display = 'block';
            manuscriptContainer.style.display = 'block';
            loadNationalities();
        }
    });

    
    
</script>

@endsection
