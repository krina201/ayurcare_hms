document.addEventListener('DOMContentLoaded', function () {
    // sweet alert for delete
    window.confirmDelete = function (accessId) {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success me-3",
                cancelButton: "btn btn-danger"
            },
            buttonsStyling: false
        });

        swalWithBootstrapButtons.fire({
            text: "Are you sure you want to remove selected Device?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, remove!",
            cancelButtonText: "No, cancel",
            reverseButtons: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the form if the access confirms
                document.getElementById(`delete-access-form-${accessId}`).submit();

                swalWithBootstrapButtons.fire({
                    text: "You have remove the selected Device!",
                    icon: "success"
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                swalWithBootstrapButtons.fire({
                    text: "Remove cancelled",
                    icon: "error"
                });
            }
        });
    }

    // Sweet Alert for approve
    window.confirmApprove = function (accessId) {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success me-3",
                cancelButton: "btn btn-danger"
            },
            buttonsStyling: false
        });

        swalWithBootstrapButtons.fire({
            text: "Are you sure you want to approve this Device?",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Yes, approve!",
            cancelButtonText: "No, cancel",
            reverseButtons: false
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`approve-access-form-${accessId}`).submit();
                swalWithBootstrapButtons.fire({
                    text: "You have approved the selected Device!",
                    icon: "success"
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                swalWithBootstrapButtons.fire({
                    text: "Approval cancelled",
                    icon: "error"
                });
            }
        });
    }

    // Sweet Alert for Log out
    window.confirmLogout = function (accessId) {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success me-3",
                cancelButton: "btn btn-danger"
            },
            buttonsStyling: false
        });

        swalWithBootstrapButtons.fire({
            text: "Are you sure you want to Logout this Device?",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Yes, Logout!",
            cancelButtonText: "No, cancel",
            reverseButtons: false
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`logout-access-form-${accessId}`).submit();
                swalWithBootstrapButtons.fire({
                    text: "You have Logout the selected Device!",
                    icon: "success"
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                swalWithBootstrapButtons.fire({
                    text: "Approval cancelled",
                    icon: "error"
                });
            }
        });
    }

});