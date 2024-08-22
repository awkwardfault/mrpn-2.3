<?php
session_start();
if (!isset($_SESSION['email'])) {
   header('Location: login.html');
   exit();
}

$userEmail = trim($_SESSION['email']);
$userId = $_SESSION['user_id']; // get the logged-in user's ID
?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Local Chat</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
      integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
   <!-- <link rel="stylesheet" href="style.css"> -->
   <style>
      /* General styles */
      .user-avatar {
         width: 80px;
         height: 80px;
         border-radius: 50%;
         background-color: #ccc;
         justify-content: center;
         align-items: center;
         text-align: center;
         font-weight: bold;
         display: flex;
      }

      .user-id {
         margin: 5px 0;
         font-size: 0.9em;
         color: #999999;
      }

      .user-email {
         font-size: 1em;
         color: #333333;
      }

      /* .user-info p {
         margin: 0;
      } */

      .icon-img {
         width: 24px;
         height: 24px;
      }

      .user-profile-container {
         display: flex;
         flex-direction: column;
         align-items: center;
         padding: 20px;
      }

      .logout-btn {
         margin-top: 15px;
      }

      .setting-btn {
         background: transparent;
         border: none;
      }

      /* Chat styles */
      .chat-container {
         display: flex;
         flex-direction: column;
         height: 100vh;
         position: relative;
      }

      .messages {
         flex: 1;
         overflow-y: auto;
      }

      .input-container {
         display: flex;
         gap: 10px;
      }

      /* Remove friend button */
      .remove_button {
         background-color: #d8eefe;
         border: none;
         cursor: pointer;
         transition: background-color 0.3s;
         margin-left: auto;
         border-radius: 50px;
      }

      .remove_button:hover {
         background-color: #0056b3;
      }

      .remove_button img {
         width: 18px;
         height: 18px;
      }


      .message {
         max-width: 70%;
         padding: 8px;
         border-radius: 20px;
         display: flex;
         flex-direction: column;
         word-wrap: break-word;
      }

      .message p {
         margin-bottom: 3px;
         margin-top: 2px;
         margin-left: 2px;
         margin-right: 2px;
      }

      /* Styling for user messages */
      .your-message-container {
         display: flex;
         justify-content: flex-end;
         flex-direction: row-reverse;
         padding-top: 10px;
      }

      .your-message-container .avatar {
         height: 40px;
         width: 40px;
         border-radius: 50%;
         background-color: #ccc;
         text-align: center;
         margin-left: 10px;
         margin-right: 0;
         display: flex;
         text-align: center;
         justify-content: center;
         align-items: center;
      }

      .your-message {
         background-color: #dcf8c6;
         margin-left: auto;
         align-items: flex-end;
      }

      /* Styling for other messages */
      .other-message-container {
         justify-content: flex-start;
         flex-direction: row;
         display: flex;
         padding-top: 10px;
      }

      .other-message-container .avatar {
         height: 40px;
         width: 40px;
         border-radius: 50%;
         background-color: #ccc;
         text-align: center;
         margin-right: 10px;
         margin-left: 0;
         display: flex;
         justify-content: center;
         align-items: center;
      }

      .other-message {
         background-color: #D0E7FF;
         border: 1px solid #D0E7FF;
      }

      /* Timestamp styling */
      .timestamp {
         font-size: 0.75em;
         color: #999;
         margin-top: 2px;
         text-align: left;
      }

      /* Chat header styling */
      .chat-header {
         display: flex;
         align-items: center;
         padding: 10px;
         border-bottom: 1px solid #ddd;
         background: #d8eefe;
      }

      /* Avatar in chat header */
      .header-avatar {
         width: 40px;
         height: 40px;
         border-radius: 50%;
         background-color: #ccc;
         display: flex;
         justify-content: center;
         align-items: center;
         font-weight: bold;
         color: #fff;
         margin-right: 10px;
      }

      .clear-btn {
         /* margin-left: 10px; */
         padding: 10px;
         background: transparent;
         border: none;
         cursor: pointer;
         border-radius: 3px;
         display: block;
      }

      .clear-btn img {
         width: 22px;
         height: 22px;
      }

      .contact {
         padding: 10px;
         cursor: pointer;
         border-bottom: 1px solid #ddd;
      }

      .contact:hover {
         background-color: #f0f0f0;
         cursor: pointer;
      }

      .sidebar {
         height: 100vh;
         overflow-y: auto;
      }

      /* Mobile styles */
      @media (max-width: 767px) {

         .chat-app {
            height: 100%;
            width: 100%;
         }

         /* .user-profile-container,
         .contacts-container,
         .chat-container {} */

         .user-profile-container {
            margin-top: 50px;
            height: 100vh;
            position: fixed;
            align-items: center;
            justify-content: center;
         }

         .chat-container {
            width: 100%;
            flex-direction: column;
            overflow-y: hidden;
            margin-top: 50px;
            margin-bottom: 0px;
            position: fixed;
            height: 95vh;
         }

         .sidebar,
         .chat-container {
            display: none;
         }

         .sidebar {
            width: 100%;
            margin-top: 50px;
            height: calc(100vh - 56px);
            overflow-y: hidden;
         }

         .contacts-container {
            display: block;
            height: 100%;
            margin-top: 50px;
            width: 100%;
            overflow-y: auto;
         }

         .mobile-nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #fff;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
            z-index: 10;
            justify-content: space-around;
            padding: 10px 0;
         }

         .mobile-nav .btn {
            background-color: #fff;
         }

         .mobile-nav .btn .profile-icon,
         .contacts-icon {
            width: 25px;
            height: 25px;
         }

         .sidebar.show,
         .contacts-container.show,
         .chat-container.show {
            display: block;
            /* height: 100vh; */
         }
      }
   </style>
</head>

<body>

   <div class="chat-app">
      <div class="d-flex h-100">
         <nav id="user-profile-container"
            class="user-profile-container sidebar col-md-3 col-lg-2 p-0 bg-light border-end">
            <div class="d-flex flex-column h-100 p-3">
               <div class="user-avatar"></div>
               <div class="user-info">
                  <p class="user-id"><?php echo $userId; ?></p>
                  <p class="user-name"></p>
                  <p class="user-email"><?php echo $userEmail; ?></p>
               </div>
               <div class="mt-auto">
                  <a href="settings.html" class="my-2">
                     <button id="setting" class="setting-btn btn btn-primary w-100">
                        <img src="settings.png" alt="Settings" class="icon-img">
                     </button>
                  </a>
                  <button id="logout" class="logout-btn btn btn-danger w-100">Logout</button>
               </div>
            </div>
         </nav>
         <div id="contacts-container" class="contacts-container col-md-3 col-lg-2 p-3 bg-light border-end">
            <div class="d-flex justify-content-between align-items-center mb-3">
               <button id="show-friends" class="btn btn-outline-primary">
                  <img src="friends.png" alt="Friends" class="icon-img">
               </button>
               <button id="show-requests" class="btn btn-outline-primary">
                  <img src="rq.png" alt="Requests" class="icon-img">
               </button>
            </div>
            <input type="text" id="search" placeholder="Search contacts..." class="form-control mb-3" />
            <div class="contacts" id="contacts"></div>
         </div>
         <main id="chat-container" class="chat-container col-md-6 col-lg-8 p-3">
            <div class="chat-header" id="chat-header"> </div>
            <div class="messages overflow-auto" id="messages"></div>
            <form id="form" class="input-container d-flex mt-3">
               <input id="input" autocomplete="off" placeholder="Type a message..." class="form-control me-2" />
               <button class="btn btn-primary">Send</button>
            </form>
         </main>
      </div>

      <!-- Mobile navigation buttons -->
      <div class="mobile-nav d-flex d-md-none">
         <p>ChatApp</p>
         <button id="toggle-user-profile" class="btn btn-primary">
            <img class="profile-icon" src="profile_icon.png" alt="Profile">
         </button>
         <button id="toggle-contacts" class="btn btn-primary">
            <img class="contacts-icon" src="contacts_icon.png" alt="Contacts">
         </button>
      </div>

   </div>

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

      document.addEventListener('DOMContentLoaded', function () {
         var form = document.getElementById('form');
         var input = document.getElementById('input');
         var messages = document.getElementById('messages');
         var contacts = document.getElementById('contacts');
         var chatHeader = document.getElementById('chat-header');
         var search = document.getElementById('search');
         var showFriendsBtn = document.getElementById('show-friends');
         var showRequestsBtn = document.getElementById('show-requests');


         // Fetch and display user info
         function fetchUserInfo() {
            fetch('get_user_info.php')
               .then(response => response.json())
               .then(data => {
                  if (data.status === 'success') {
                     // Update user profile section with fetched data
                     const user = data.data;
                     document.querySelector('.user-avatar').textContent = user.last_name[0];
                     document.querySelector('.user-id').textContent = `UID: ${user.user_id}`;
                     document.querySelector('.user-name').textContent = `${user.first_name} ${user.last_name}`;
                     document.querySelector('.user-email').textContent = user.email;
                  } else {
                     console.error('Error fetching user info:', data.message);
                  }
               })
               .catch(error => console.error('Error:', error));
         }

         // Fetch friends data
         function fetchFriends() {
            return fetch('fetch_friends.php')
               .then(response => response.json());
         }

         // Fetch friend requests data
         function fetchRequests() {
            return fetch('fetch_requests.php')
               .then(response => response.json())
               .then(data => {
                  console.log('Fetched data:', data);  // Log the fetched data
                  return Array.isArray(data) ? data : []
               })
               .catch(error => {
                  console.error('Error fetching requests:', error);
                  return [];
               });
         }
         var msg_interval = setInterval(fetchMessages, 3000);
         function loadChat() {
            const messagesContainer = document.getElementById('messages');
            console.log('Loading chat, scrolling to bottom.');
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
         }

         // Fetch messages between user and selected contact
         function fetchMessages() {
            return new Promise((resolve, reject) => {
               if (selectedContactId) {
                  fetch(`fetch_messages.php?receiver_id=${selectedContactId}`)
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

                           var avatar = document.createElement('div');
                           avatar.classList.add('avatar');
                           avatar.textContent = msg.email.charAt(0).toUpperCase();
                           item.appendChild(avatar);

                           var message = document.createElement('div');
                           message.classList.add('message');
                           if (msg.email.trim() === userEmail) {
                              message.classList.add('your-message');
                           } else {
                              message.classList.add('other-message');
                           }

                           var content = document.createElement('p');
                           content.textContent = msg.text;
                           message.appendChild(content);

                           var timestamp = document.createElement('div');
                           timestamp.classList.add('timestamp');
                           timestamp.textContent = msg.created_at;
                           message.appendChild(timestamp);

                           item.appendChild(message);
                           messages.appendChild(item);
                        });
                        resolve();
                        // messages.scrollTop = messages.scrollHeight;
                     })
                     .catch(error => {
                        (error => console.error('Error fetching messages:', error));
                        reject(error);
                     });
               } else {
                  resolve();
               }
            });
         }

         // Display contacts based on view type (friends, requests, search)
         function displayContacts(contactsData, friendsData, viewType) {
            contacts.innerHTML = ''; // Clear the container

            // Check if contactsData and friendsData are arrays
            if (!Array.isArray(contactsData)) {
               console.error('Expected contactsData to be an array, but got:', contactsData);
               return;
            }
            if (!Array.isArray(friendsData)) {
               console.error('Expected friendsData to be an array, but got:', friendsData);
               return;
            }

            // Create a Set of friend IDs for quick lookup
            const friendIds = new Set(friendsData.map(friend => friend.user_id));

            if (viewType === 'friends') {
               // Display friends
               contactsData.forEach(contact => {
                  var contactItem = document.createElement('div');
                  contactItem.classList.add('contact');
                  // contactItem.textContent = contact.email;
                  contactItem.textContent = contact.user_id;
                  contactItem.dataset.id = contact.user_id;
                  contactItem.dataset.email = contact.email;

                  contactItem.addEventListener('click', function () {
                     const messagesContainer = document.getElementById('messages');
                     if (selectedContactId === contact.user_id) {
                        // Deselect the contact
                        console.log(`Deselecting contact: ${contact.user_id}`);
                        selectedContactId = null;
                        messagesContainer.innerHTML = '<p>No messages</p>';
                        updateChatHeader(null);
                        clearInterval(msg_interval);
                     } else {
                        // Select a new contact
                        console.log(`selecting contact: ${contact.user_id}`);
                        selectedContactId = contact.user_id;
                        updateChatHeader(contact);
                        fetchMessages().then(() => {
                           loadChat();
                        });
                        clearInterval(msg_interval);
                        msg_interval = setInterval(fetchMessages, 3000);
                     }
                  });

                  contacts.appendChild(contactItem);
               });
            } else if (viewType === 'requests') {
               // Display requests
               contactsData.forEach(request => {
                  var contactItem = document.createElement('div');
                  contactItem.classList.add('contact');
                  contactItem.textContent = request.email;
                  contactItem.dataset.id = request.user_id;
                  contactItem.dataset.email = request.email;

                  var acceptButton = document.createElement('button');
                  acceptButton.textContent = 'Accept';
                  acceptButton.onclick = function () {
                     handleRequest(request.user_id, 'accept');
                  };

                  var rejectButton = document.createElement('button');
                  rejectButton.textContent = 'Reject';
                  rejectButton.onclick = function () {
                     handleRequest(request.user_id, 'reject');
                  };

                  contactItem.appendChild(acceptButton);
                  contactItem.appendChild(rejectButton);

                  contacts.appendChild(contactItem);
               });
            } else if (viewType === 'search') {
               // Display search results
               contactsData.forEach(contact => {
                  var contactItem = document.createElement('div');
                  contactItem.classList.add('contact');
                  contactItem.textContent = contact.email;
                  contactItem.dataset.id = contact.user_id;
                  contactItem.dataset.email = contact.email;

                  if (friendIds.has(contact.user_id)) {
                     // Contact is a friend, no button needed
                     contactItem.addEventListener('click', function () {
                        selectedContactId = contact.user_id;
                        updateChatHeader(contact);
                        fetchMessages();
                     });
                  } else {
                     // Contact is not a friend, show "Add Friend" button
                     var addButton = document.createElement('button');
                     addButton.textContent = 'Add Friend';
                     addButton.onclick = function () {
                        addFriend(contact.user_id);
                     };
                     contactItem.appendChild(addButton);
                  }
                  contacts.appendChild(contactItem);
               });
            }
         }

         // Function to fetch all contacts and friends, then call displayContacts
         function fetchAndDisplayContacts() {
            Promise.all([fetch('fetch_contacts.php').then(response => response.json()), fetch('fetch_friends.php').then(response => response.json())])
               .then(([contactsData, friendsData]) => {
                  displayContacts(contactsData, friendsData);
               })
               .catch(error => console.error('Error fetching contacts or friends:', error));
         }

         // Update chat header with selected contact information
         function updateChatHeader(contact) {
            chatHeader.innerHTML = '';

            var avatar = document.createElement('div');
            avatar.classList.add('header-avatar');
            avatar.textContent = contact.last_name[0];

            var name = document.createElement('div');
            name.classList.add('header-name');
            name.textContent = `${contact.first_name} ${contact.last_name}`;

            var clear_btn = document.createElement('button');
            clear_btn.classList.add('clear-btn');
            var icon = document.createElement('img');
            icon.src = 'back.png';
            icon.alt = 'Back';
            clear_btn.appendChild(icon);
            clear_btn.onclick = function () {
               if (isMobileView()) {
                  const userProfileContainer = document.getElementById('user-profile-container');
                  const contactsContainer = document.querySelector('.contacts-container');

                  if (userProfileContainer && contactsContainer) {
                     document.getElementById('chat-container').style.display = 'none';
                     userProfileContainer.style.display = 'none';
                     contactsContainer.style.display = 'block';
                  } else {
                     console.error('Mobile view containers not found');
                  }
               }
               console.log(`Deselecting contact: ${contact.user_id}`);
               selectedContactId = null;
               const messagesContainer = document.getElementById('messages');
               messagesContainer.innerHTML = '<p>No messages</p>';
               updateChatHeader(null);
               clearInterval(msg_interval);

            }

            var remove_Button = document.createElement('button');
            remove_Button.classList.add('remove_button');
            // remove_Button.innerText = 'Remove Friend';
            var icon = document.createElement('img');
            icon.src = 'remove.png';  // Set the source of the icon
            icon.alt = 'Remove Friend';
            remove_Button.appendChild(icon);
            remove_Button.onclick = function () {
               removeFriend(contact.user_id);
               fetchFriends();
               fetchAndDisplayContacts();
               updateChatHeader(null);
            };
            chatHeader.appendChild(clear_btn);
            chatHeader.appendChild(avatar);
            chatHeader.appendChild(name);
            chatHeader.appendChild(remove_Button);
         }
         // var clear_btn = document.createElement('button');
         // clear_btn.classList.add('clear_btn');
         // chatHeader.appendChild(clear_btn);
         // function deselect_contact() {
         //    selectedContactId = null; // Deselect contact
         //    updateChatHeader({ name: 'Select a contact' }); // Set header to default state
         //    document.getElementById('messages').innerHTML = '<p>No messages</p>'; // Clear chat messages

         //    // clear_btn.onclick = function () {
         //    //    const messagesContainer = document.getElementById('messages');
         //    //    messagesContainer.innerHTML = '<p>No messages</p>';
         //    // }
         // }
         // clear_btn.addEventListener('click', function () {
         //    deselect_contact();
         // });


         // Search contacts
         function searchContacts(query) {
            fetch(`search_users.php?query=${encodeURIComponent(query)}`)
               .then(response => response.json())
               .then(data => {
                  // Fetch the friends list to compare
                  fetchFriends().then(friendsData => {
                     displayContacts(data, friendsData, 'search');
                  });
               })
               .catch(error => console.error('Error fetching search results:', error));
         }

         // Add a friend
         function addFriend(contactId) {
            console.log('Contact ID:', contactId); // Print contact ID to the console

            fetch('add_friend.php', {
               method: 'POST',
               headers: {
                  'Content-Type': 'application/json'
               },
               body: JSON.stringify({
                  user_id: contactId
               })
            })
               .then(response => response.json())
               .then(data => {
                  if (data.status === 'success') {
                     alert('Friend request sent successfully.');
                     // Refresh contacts or update UI
                     if (showFriendsBtn.classList.contains('active')) {
                        fetchFriends(); // Refresh friends list                       
                     } else if (showRequestsBtn.classList.contains('active')) {
                        fetchRequests(); // Refresh requests list
                     } else {
                        searchContacts(search.value); // Refresh search results
                     }
                  } else {
                     alert('Error sending friend request: ' + data.message);
                  }
               })
               .catch(error => console.error('Error adding friend:', error));
         }

         // Handle friend request (accept/reject)
         function handleRequest(userId, action) {
            fetch('handle_requests.php', {
               method: 'POST',
               headers: {
                  'Content-Type': 'application/json'
               },
               body: JSON.stringify({
                  user_id: userId,
                  action: action
               })
            })
               .then(response => response.json())
               .then(data => {
                  if (data.status === 'success') {
                     if (action === 'accept') {
                        fetchRequests(); // Refresh requests
                        fetchAndDisplayContacts(); // Refresh contacts container

                     } else {
                        fetchFriends(); // Refresh friends
                        fetchAndDisplayContacts(); // Refresh contacts container
                     }
                  } else {
                     alert(data.message);
                  }
               })
               .catch(error => console.error('Error handling request:', error));
         }

         // Function to remove a friend
         function removeFriend(friendUserId) {
            fetch('remove_friend.php', {
               method: 'POST',
               headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
               body: `friend_user_id=${friendUserId}`
            })
               .then(response => response.json())
               .then(data => {
                  if (data.status === 'success') {
                     alert('Friend removed');
                     // Update the UI to reflect that the friend has been removed
                     document.querySelector(`#friend-${friendUserId}`).remove(); // Example of removing friend entry
                  } else {
                     alert('Failed to remove friend');
                  }
               });
         }

         // Event listeners
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

         // Search event listener
         search.addEventListener('input', function () {
            var query = search.value;
            if (query.length > 0) {
               searchContacts(query);
            } else {
               fetchFriends(); // Reload contacts if search input is empty
            }
         });

         // Function to check if the screen is in mobile view
         function isMobileView() {
            return window.matchMedia("(max-width: 767px)").matches;
         }

         // Toggle user profile container
         document.getElementById('toggle-user-profile').addEventListener('click', function () {
            if (isMobileView()) {
               const userProfileContainer = document.getElementById('user-profile-container');
               const contactsContainer = document.querySelector('.contacts-container');
               const chatContainer = document.getElementById('chat-container');
               if (userProfileContainer.style.display === 'none' || userProfileContainer.style.display === '') {
                  userProfileContainer.style.display = 'flex';
                  contactsContainer.style.display = 'none';
                  chatContainer.style.display = 'none';
               } else {
                  userProfileContainer.style.display = 'none';
                  contactsContainer.style.display = 'block';
               }
            }
         });

         function open_contacts() {
            // Toggle contacts container
            // document.getElementById('toggle-contacts').addEventListener('click', function () {
            if (isMobileView()) {
               const userProfileContainer = document.getElementById('user-profile-container');
               const contactsContainer = document.querySelector('.contacts-container');

               document.getElementById('chat-container').style.display = 'none'; // Ensure chat container is hidden
               userProfileContainer.style.display = 'none';
               contactsContainer.style.display = 'block';
            }
            // });
         }
         document.getElementById('toggle-contacts').addEventListener('click', function () {
            open_contacts();
         });
         // Show chat container when a contact is clicked
         document.getElementById('contacts').addEventListener('click', function (e) {
            if (isMobileView() && e.target.closest('.contact')) {
               const userProfileContainer = document.getElementById('user-profile-container');
               const contactsContainer = document.querySelector('.contacts-container');
               const chatContainer = document.getElementById('chat-container');

               chatContainer.style.display = 'flex';
               userProfileContainer.style.display = 'none';
               contactsContainer.style.display = 'none';
            }
         });

         // Listen for window resize events to reapply the correct display settings
         window.addEventListener('resize', function () {
            if (!isMobileView()) {
               document.getElementById('user-profile-container').style.display = 'block';
               document.querySelector('.contacts-container').style.display = 'block';
               document.getElementById('chat-container').style.display = 'block';
            }
         });

         // Event listeners for buttons
         showFriendsBtn.addEventListener('click', function () {
            fetch('fetch_contacts.php')
               .then(response => response.json())
               .then(contactsData => {
                  fetch('fetch_friends.php')
                     .then(response => response.json())
                     .then(friendsData => {
                        displayContacts(contactsData, friendsData, 'friends');
                     });
               });
         });

         showRequestsBtn.addEventListener('click', function () {
            fetchRequests().then(requestsData => {
               displayContacts(requestsData, [], 'requests');
            });
         });


         // Fetch initial user info and contacts
         fetchUserInfo();
         // fetchAndDisplayContacts();
         // Fetch messages every 3 seconds
         // const msg_interval = setInterval(fetchMessages, 3000);
         fetchFriends(); // Fetch contacts initially

         // Logout functionality
         document.getElementById('logout').addEventListener('click', function () {
            fetch('logout.php')
               .then(response => response.json())
               .then(data => {
                  if (data.status === 'success') {
                     window.location.href = 'login.html';
                  } else {
                     alert('Error logging out');
                  }
               })
               .catch(error => console.error('Error logging out:', error));
         });
      });

   </script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
      crossorigin="anonymous"></script>
</body>

</html>