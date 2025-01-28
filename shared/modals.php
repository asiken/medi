<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reusable Modal</title>
    <style>
        /* Backdrop */
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        .modal-backdrop.show {
            display: flex;
            opacity: 1;
        }

        /* Modal Container */
        .modal {
            background-color: white;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            padding: 1.5rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            transform: scale(0.9);
            transition: transform 0.3s ease-in-out;
        }

        .modal-backdrop.show .modal {
            transform: scale(1);
        }

        /* Modal Header */
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
        }

        .modal-header .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #333;
        }

        .modal-header .close-btn:hover {
            color: #007BFF;
        }

        /* Modal Body */
        .modal-body {
            margin-bottom: 1rem;
            font-size: 1rem;
            color: #555;
        }

        /* Modal Footer */
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
        }

        .modal-footer .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }

        .modal-footer .btn.primary {
            background-color: #007BFF;
            color: white;
        }

        .modal-footer .btn.secondary {
            background-color: #f8f9fa;
            color: #333;
        }

        .modal-footer .btn.primary:hover {
            background-color: #0056b3;
        }

        .modal-footer .btn.secondary:hover {
            background-color: #e2e6ea;
        }
    </style>
</head>
<body>
    <!-- Modal Backdrop -->
    <div id="modalBackdrop" class="modal-backdrop">
        <!-- Modal -->
        <div class="modal">
            <div class="modal-header">
                <h2 id="modalTitle">Modal Title</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body" id="modalBody">
                This is the modal content.
            </div>
            <div class="modal-footer" id="modalFooter">
                <button class="btn secondary" onclick="closeModal()">Cancel</button>
                <button class="btn primary" onclick="confirmAction()">OK</button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        const modalBackdrop = document.getElementById('modalBackdrop');
        const modalTitle = document.getElementById('modalTitle');
        const modalBody = document.getElementById('modalBody');
        const modalFooter = document.getElementById('modalFooter');

        function openModal(title = 'Modal Title', body = 'This is the modal content.', buttons = []) {
            modalTitle.innerText = title;
            modalBody.innerHTML = body;

            // Clear existing buttons
            modalFooter.innerHTML = '';

            // Add buttons dynamically
            buttons.forEach(button => {
                const btn = document.createElement('button');
                btn.className = `btn ${button.type || 'secondary'}`;
                btn.innerText = button.text;
                btn.onclick = button.action || closeModal;
                modalFooter.appendChild(btn);
            });

            modalBackdrop.classList.add('show');
        }

        function closeModal() {
            modalBackdrop.classList.remove('show');
        }

        function confirmAction() {
            alert('Action confirmed!');
            closeModal();
        }
    </script>
</body>
</html>

