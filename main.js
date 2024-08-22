<script>
      // Pass PHP variables to JavaScript
   const userEmail = "<?php echo $userEmail; ?>";
   const userId = "<?php echo $userId; ?>";
   let selectedContactId = null;

   // Check if user is logged in
   fetch('check_session.php')
         .then(response => response.json())
         .then(data => {
            if (data.status !== 'logged_in') {
      window.location.href = 'login.html';
            }
         });

   // JavaScript for handling chat functionality
   document.addEventListener('DOMContentLoaded', function () {
         var form = document.getElementById('form');
   var input = document.getElementById('input');
   var messages = document.getElementById('messages');
   var contacts = document.getElementById('contacts');
   var chatHeader = document.getElementById('chat-header'); // Chat header element
   var search = document.getElementById('search'); // Search input element


   function fetchContacts() {
      fetch('fetch_contacts.php')
         .then(response => response.json())
         .then(data => {
            contacts.innerHTML = '';
            data.forEach(contact => {
               var contactItem = document.createElement('div');
               contactItem.classList.add('contact');
               contactItem.textContent = contact.email;
               contactItem.dataset.id = contact.id;
               contactItem.dataset.email = contact.email; // Add email for search filtering
               contactItem.addEventListener('click', function () {
                  selectedContactId = contact.id;
                  updateChatHeader(contact); // Update chat header with contact info
                  fetchMessages();
               });
               contacts.appendChild(contactItem);
            });
         });
         }
   function updateChatHeader(contact) {
      chatHeader.innerHTML = ''; // Clear previous content

   var avatar = document.createElement('div');
   avatar.classList.add('header-avatar');
   avatar.textContent = contact.email.charAt(0).toUpperCase();

   var name = document.createElement('div');
   name.classList.add('header-name');
   name.textContent = contact.email;

   chatHeader.appendChild(avatar);
   chatHeader.appendChild(name);
         }
   function fetchMessages() {
            if (selectedContactId) {
      fetch('fetch_messages.php?receiver_id=' + selectedContactId)
         .then(response => response.json())
         .then(data => {
            messages.innerHTML = '';
            data.forEach(msg => {
               var item = document.createElement('div');
               item.classList.add('message-container');

               if (msg.email.trim() === userEmail) {
                  item.classList.add('your-message-container');
               } else {
                  item.classList.add('other-message-container');
               }

               // Avatar
               var avatar = document.createElement('div');
               avatar.classList.add('avatar');
               avatar.textContent = msg.email.charAt(0).toUpperCase();
               item.appendChild(avatar);

               // Message
               var message = document.createElement('div');
               message.classList.add('message');

               if (msg.email.trim() === userEmail) {
                  message.classList.add('your-message');
               } else {
                  message.classList.add('other-message');
               }

               // Message Content
               var content = document.createElement('p');
               content.textContent = msg.text;
               message.appendChild(content);

               // Timestamp
               var timestamp = document.createElement('div');
               timestamp.classList.add('timestamp');
               timestamp.textContent = msg.created_at;
               message.appendChild(timestamp);

               item.appendChild(message);
               messages.appendChild(item);
            });
            messages.scrollTop = messages.scrollHeight;
         });
            }
         }

   form.addEventListener('submit', function (e) {
      e.preventDefault();
   if (input.value && selectedContactId) {
      fetch('send_message.php', {
         method: 'POST',
         headers: {
            'Content-Type': 'application/json'
         },
         body: JSON.stringify({
            message: input.value,
            receiver_id: selectedContactId
         })
      })
         .then(response => response.json())
         .then(data => {
            if (data.status === 'success') {
               input.value = '';
               fetchMessages();
            } else {
               console.error('Error sending message:', data.message);
            }
         })
         .catch(error => console.error('Error:', error));
            }
         });

   // Add event listener for search functionality
   search.addEventListener('input', function () {
            var filter = search.value.toLowerCase();
   var contactItems = contacts.getElementsByClassName('contact');
   Array.from(contactItems).forEach(function (contactItem) {
               var email = contactItem.dataset.email.toLowerCase();
   if (email.includes(filter)) {
      contactItem.style.display = '';
               } else {
      contactItem.style.display = 'none';
               }
            });
         });
   setInterval(fetchMessages, 3000); // Fetch messages every 3 seconds
   fetchContacts(); // Fetch contacts initially
      });

   // Logout functionality
   document.getElementById('logout').addEventListener('click', function () {
      fetch('logout.php')
         .then(response => response.json())
         .then(data => {
            if (data.status === 'success') {
               window.location.href = 'login.html';
            }
         });
      });
</script>