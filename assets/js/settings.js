document.addEventListener ('DOMContentLoaded', function () {
  loadSchools ();
  loadPrograms ();
});

// Schools Functions
function loadSchools () {
  fetch ('../../api/settings/get_schools.php')
    .then (response => response.json ())
    .then (data => {
      if (data.status) {
        updateSchoolsTable (data.data);
        updateSchoolDropdown (data.data);
      }
    })
    .catch (error => console.error ('Error:', error));
}

function updateSchoolsTable (schools) {
  const tbody = document.getElementById ('schoolsTable');
  if (!schools.length) {
    tbody.innerHTML =
      '<tr><td colspan="2" class="text-center">No schools found</td></tr>';
    return;
  }

  tbody.innerHTML = schools
    .map (
      school => `
        <tr>
            <td>${school.school_name}</td>
            <td>
                <button class="btn btn-sm btn-outline-primary me-2" onclick="editSchool(${school.id})">
                    <i class="bi bi-pencil"></i> Edit
                </button>
            </td>
        </tr>
    `
    )
    .join ('');
}

function updateSchoolDropdown (schools) {
  const select = document.getElementById ('programSchool');
  select.innerHTML =
    '<option value="">Select School</option>' +
    schools
      .map (
        school => `<option value="${school.id}">${school.school_name}</option>`
      )
      .join ('');
}

function showSchoolModal (isEdit = false) {
  document.getElementById ('schoolModalTitle').textContent = isEdit
    ? 'Edit School'
    : 'Add School';
  document.getElementById ('schoolForm').reset ();
  document.getElementById ('schoolId').value = '';
  new bootstrap.Modal (document.getElementById ('schoolModal')).show ();
}

function editSchool (id) {
  fetch (`../../api/settings/get_school.php?id=${id}`)
    .then (response => response.json ())
    .then (data => {
      if (data.status) {
        const school = data.data;
        document.getElementById ('schoolId').value = school.id;
        document.getElementById ('schoolName').value = school.school_name;
        showSchoolModal (true);
      }
    })
    .catch (error => console.error ('Error:', error));
}

function saveSchool () {
  const schoolData = {
    id: document.getElementById ('schoolId').value,
    name: document.getElementById ('schoolName').value,
  };

  fetch ('../../api/settings/save_school.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify (schoolData),
  })
    .then (response => response.json ())
    .then (data => {
      if (data.status) {
        bootstrap.Modal
          .getInstance (document.getElementById ('schoolModal'))
          .hide ();
        loadSchools ();
        showAlert ('success', data.message);
      } else {
        showAlert ('danger', data.message);
      }
    })
    .catch (error => {
      console.error ('Error:', error);
      showAlert ('danger', 'An error occurred while saving');
    });
}

// Programs Functions
function loadPrograms () {
  fetch ('../../api/settings/get_programs.php')
    .then (response => response.json ())
    .then (data => {
      if (data.status) {
        updateProgramsTable (data.data);
      }
    })
    .catch (error => console.error ('Error:', error));
}

function updateProgramsTable (programs) {
  const tbody = document.getElementById ('programsTable');
  if (!programs.length) {
    tbody.innerHTML =
      '<tr><td colspan="5" class="text-center">No programs found</td></tr>';
    return;
  }

  tbody.innerHTML = programs
    .map (
      program => `
        <tr>
            <td>${program.program_name}</td>
            <td>${program.school_name}</td>
            <td>${program.duration} Yrs</td>
            <td>${formatCurrency (program.tuition_fee)}</td>
            <td>
                <button class="btn btn-sm btn-outline-primary me-2" onclick="editProgram(${program.id})">
                    <i class="bi bi-pencil"></i> Edit
                </button>
            </td>
        </tr>
    `
    )
    .join ('');
}

function showProgramModal (isEdit = false) {
  document.getElementById ('programModalTitle').textContent = isEdit
    ? 'Edit Program'
    : 'Add Program';
  document.getElementById ('programForm').reset ();
  document.getElementById ('programId').value = '';
  new bootstrap.Modal (document.getElementById ('programModal')).show ();
}

function editProgram (id) {
  fetch (`../../api/settings/get_program.php?id=${id}`)
    .then (response => response.json ())
    .then (data => {
      if (data.status) {
        const program = data.data;
        document.getElementById ('programId').value = program.id;
        document.getElementById ('programName').value = program.program_name;
        document.getElementById ('programSchool').value = program.school_id;
        document.getElementById ('programDuration').value = program.duration;
        document.getElementById ('programFee').value = program.tuition_fee;
        showProgramModal (true);
      }
    })
    .catch (error => console.error ('Error:', error));
}

function saveProgram () {
  const programData = {
    id: document.getElementById ('programId').value,
    name: document.getElementById ('programName').value,
    school_id: document.getElementById ('programSchool').value,
    duration: document.getElementById ('programDuration').value,
    tuition_fee: document.getElementById ('programFee').value,
  };

  fetch ('../../api/settings/save_program.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify (programData),
  })
    .then (response => response.json ())
    .then (data => {
      if (data.status) {
        bootstrap.Modal
          .getInstance (document.getElementById ('programModal'))
          .hide ();
        loadPrograms ();
        showAlert ('success', data.message);
      } else {
        showAlert ('danger', data.message);
      }
    })
    .catch (error => {
      console.error ('Error:', error);
      showAlert ('danger', 'An error occurred while saving');
    });
}

function formatCurrency (amount) {
  return new Intl.NumberFormat ('en-US', {
    style: 'currency',
    currency: 'ZMW',
    currencyDisplay: 'narrowSymbol',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format (amount);
}

function showAlert (type, message) {
  const alertDiv = document.createElement ('div');
  alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
  alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
  document.body.appendChild (alertDiv);
  setTimeout (() => alertDiv.remove (), 3000);
}

function logout () {
  fetch ('../../api/logout.php')
    .then (response => response.json ())
    .then (data => {
      if (data.status) {
        window.location.href = '../login.php';
      }
    })
    .catch (error => console.error ('Error:', error));
}
