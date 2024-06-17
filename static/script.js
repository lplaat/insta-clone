let posts = {};
function initAllPosts() {
    // post initializer
    let postElements = document.getElementsByClassName('post-item');
    
    for(let i = 0; postElements.length > i; i++) {
        // Retrieve data from the post
        let post = JSON.parse(postElements[i].getElementsByClassName('post-data')[0].textContent);
        post['element'] = postElements[i];

        let id = post['element'].id;
        posts[id] = post;
        
        // Set event handlers
        let likeButton = post['element'].getElementsByClassName('like-button')[0];
        let likeCounter = post['element'].getElementsByClassName('like-counter')[0];
        likeButton.onclick = () => {likePostButton(likeButton, id, likeCounter)};
    }
}

function likePostButton(element, id, likeCounter){
    // Sets the new icon
    posts[id]['isLiked'] = !posts[id]['isLiked'];
    if(posts[id]['isLiked']) {
        element.innerHTML = '<path fill="#FF0000" d="M47.6 300.4L228.3 469.1c7.5 7 17.4 10.9 27.7 10.9s20.2-3.9 27.7-10.9L464.4 300.4c30.4-28.3 47.6-68 47.6-109.5v-5.8c0-69.9-50.5-129.5-119.4-141C347 36.5 300.6 51.4 268 84L256 96 244 84c-32.6-32.6-79-47.5-124.6-39.9C50.5 55.6 0 115.2 0 185.1v5.8c0 41.5 17.2 81.2 47.6 109.5z"/>';
    }else {
        element.innerHTML = '<path fill="#FF0000" d="M225.8 468.2l-2.5-2.3L48.1 303.2C17.4 274.7 0 234.7 0 192.8v-3.3c0-70.4 50-130.8 119.2-144C158.6 37.9 198.9 47 231 69.6c9 6.4 17.4 13.8 25 22.3c4.2-4.8 8.7-9.2 13.5-13.3c3.7-3.2 7.5-6.2 11.5-9c0 0 0 0 0 0C313.1 47 353.4 37.9 392.8 45.4C462 58.6 512 119.1 512 189.5v3.3c0 41.9-17.4 81.9-48.1 110.4L288.7 465.9l-2.5 2.3c-8.2 7.6-19 11.9-30.2 11.9s-22-4.2-30.2-11.9zM239.1 145c-.4-.3-.7-.7-1-1.1l-17.8-20c0 0-.1-.1-.1-.1c0 0 0 0 0 0c-23.1-25.9-58-37.7-92-31.2C81.6 101.5 48 142.1 48 189.5v3.3c0 28.5 11.9 55.8 32.8 75.2L256 430.7 431.2 268c20.9-19.4 32.8-46.7 32.8-75.2v-3.3c0-47.3-33.6-88-80.1-96.9c-34-6.5-69 5.4-92 31.2c0 0 0 0-.1 .1s0 0-.1 .1l-17.8 20c-.3 .4-.7 .7-1 1.1c-4.5 4.5-10.6 7-16.9 7s-12.4-2.5-16.9-7z"/>';
    }

    // Increment or decrement the like counter
    likeCounter.innerHTML = Number(likeCounter.innerHTML) + (posts[id]['isLiked'] ? 1 : -1);

    // Sends the like action to the server
    fetch("/post/" + id + '/like', {
        method: "POST",
        body: JSON.stringify({ liked: posts[id]['isLiked'] }),
        headers: {
            "Content-type": "application/json; charset=UTF-8"
        }
    });
}

function followUserButton(id, isPrivate){
    // Follows user action button
    const followButton = document.getElementsByClassName('follow-button')[0];
    const followCount = document.getElementsByClassName('follower-count')[0];

    let followingStatus;
    let requested;
    if (!isPrivate) {
        if(followButton.textContent == 'follow') {
            followingStatus = true;
            followButton.innerHTML = '<b>unfollow</b>';
            followButton.className = followButton.className.replace('is-success', 'is-danger');
        } else {
            followingStatus = false;
            followButton.innerHTML = '<b>follow</b>';
            followButton.className = followButton.className.replace('is-danger', 'is-success');
        }

        followCount.innerHTML = Number(followCount.textContent) - (followingStatus ? -1 : 1);

        // Sends the follow action to the server
        fetch("/user/" + id + '/follow', {
            method: "POST",
            body: JSON.stringify({ following:  followingStatus}),
            headers: {
                "Content-type": "application/json; charset=UTF-8"
            }
        });
    } else {
        if (followButton.textContent == 'follow') {
            requested = true;
            followButton.innerHTML = '<b>requested</b>';
            followButton.className = followButton.className.replace('is-success', 'placeholder');
        } else if (followButton.textContent == 'requested'){
            requested = false;
            followButton.innerHTML = '<b>follow</b>';
            followButton.className = followButton.className.replace('placeholder', 'is-success');
        } else if (followButton.textContent == "unfollow") {
            followButton.innerHTML = '<b>follow</b>';
            followButton.className = followButton.className.replace('is-danger', 'is-success');
        }

        // Sends the follow request to the server
        fetch("/user/" + id + "/follow", {
            method: "POST",
            body: JSON.stringify({requested: requested}),
            headers: {
                "Content-type": "application/json; charset=UTF-8"
            }
        });
    }
}

function acceptFollowButton(event, id) {
    // Accept follow request
    let accepted = true;
    fetch("/user/" + id + "/follow", {
        method: "POST",
        body: JSON.stringify({accepted: accepted}),
        headers: {
            "Content-type": "application/json; charset=UTF-8"
        }
    });

    event['target'].offsetParent.remove();
}

function declineFollowButton(event, id) {
    // decline follow request
    let declined = true;
    fetch("/user/" + id + "/follow", {
        method: "POST",
        body: JSON.stringify({declined: declined}),
        headers: {
            "Content-type": "application/json; charset=UTF-8"
        }
    });

    event['target'].offsetParent.remove();
}

function goTo(link) {
    // Goes to that link
    window.location.pathname = link;
}

let token = '';
let noMorePosts = false;
let loadingPosts = false;
async function loadPosts(settings){
    // Check if were not already loading posts
    if(loadingPosts) return;
    loadingPosts = true;
    
    // Create feed url
    let url = "/feed?token=" + token + "&type=" + settings['type'];
    if(settings['type'] != 'any') {
        url += '&data=' + settings['data'];
    }

    // Load in posts from the feed end-point
    let response = await fetch(url , {
        method: "GET",
        headers: {
            "Content-type": "application/json; charset=UTF-8"
        }
    });

    let data = await response.json();
    token = data['token'];

    for(let i = 0; data['posts'].length > i; i++) {
        // Load in each new post
        holder[0].innerHTML += data['posts'][i];
    }

    if(data['posts'].length == 0) {
        // Stop sending more feed request
        noMorePosts = true;
    }

    // Set a timeout to enable loading posts again
    setTimeout(() => {
        loadingPosts = false;
    }, 500);

    // Activate all posts actions
    initAllPosts();
}

// Check for a post holder if so load in posts
let holder = document.getElementsByClassName('post-holder');
if(holder.length != 0) {
    let settings = JSON.parse(holder[0].getElementsByClassName('feed-settings')[0].textContent);
    loadPosts(settings);

    // Event listener to check to load more posts
    window.addEventListener('scroll', function() {
        let scrollPosition = window.scrollY || document.documentElement.scrollTop;
        let totalHeight = document.documentElement.scrollHeight;
        let windowHeight = window.innerHeight;
  
        if (scrollPosition + windowHeight >= totalHeight - 5000) {
            loadPosts(settings);
        }
    });
}

function editProfile(){
    // Sets the elements for editing the user profile
    let innerValue = document.getElementsByClassName('user-biography')[0].innerText;
    document.getElementsByClassName('user-biography')[0].innerHTML = "<textarea class=\"textarea has-fixed-size user-biography-input\" name=\"caption\" placeholder=\"Edit biography\">" + innerValue + "</textarea>";
    document.getElementsByClassName('edit-profile-button')[0].style.display = "none";
    document.getElementsByClassName('update-profile-button')[0].style.display = "block";
}

async function uploadProfileEdit(id) {
    // Update profile with api call
    await fetch("/user/" + id + '/edit', {
        method: "POST",
        body: JSON.stringify({ biography: document.getElementsByClassName('user-biography-input')[0].value }),
        headers: {
            "Content-type": "application/json; charset=UTF-8"
        }
    });
    goTo(window.location.pathname);
}

function openFileInputs(){
    // Adds image to the post upload screen
    if(imageCount < 4) {
        document.getElementById('fileInputs').click();
    }
}

let imageCount = 0;
let images = {};
function updateImageSpacing(){
    // Update image spacing for image upload
    const elements = document.getElementsByClassName('image-figure');

    for(let i = 0; i < elements.length; i++){
        let css;
        if(imageCount == 1){
            css = "width: 100%;";
        }else {
            css = "width: calc(50% - 8px);";
        }

        elements[i].style = css;
    }
}

function removeImage(figure, container, index) {
    // Removes images form upload screen
    figure.remove();
    imageCount -= 1;

    if(imageCount == 0) {
        container.classList = "image-previews";
    }else if(imageCount < 4) {
        document.getElementsByClassName('upload-image-button')[0].removeAttribute('disabled')
    }

    delete images[index];
}

function handleInputFileChange(event) {
    // Displays images that are uploaded
    let files = event.target.files;
    const container = document.getElementsByClassName('image-previews')[0];
    const uploadImageButton = document.getElementsByClassName('upload-image-button')[0];

    for (const file of files) {
        if(imageCount > 3) {
            uploadImageButton.setAttribute('disabled', true);
            return;
        } else {
            imageCount += 1;
        }
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e, imageIndex) {
                if(imageCount == 4) {
                    uploadImageButton.setAttribute('disabled', true);
                }

                const img = document.createElement('img');
                const figure = document.createElement('figure');
                const button = document.createElement('a');
                const id = String(Math.floor(Math.random() * 100000));

                img.src = e.target.result;
                figure.classList = "image image-figure";
                button.classList = "button is-white right-side top-8";
                button.innerHTML = "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 384 512\" class=\"icon is-medium\"><path fill=\"#000000\" d=\"M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z\"/></svg>";

                let css;
                if(imageCount == 1){
                    css = "width: 100%;";
                    container.classList = "image-previews box";
                }else {
                    css = "width: calc(50% - 8px);";
                }
                figure.style = css;

                button.onclick = () => {removeImage(figure, container, id)};
                images[id] = file;
                figure.appendChild(img);
                figure.appendChild(button);
                container.appendChild(figure);
            }
            reader.readAsDataURL(file);
        }
    }

    updateImageSpacing();
}

document.addEventListener('DOMContentLoaded', () => {
    // Make a event lisper for when someone wants to upload a post 
    let formElements = document.getElementsByClassName('postUpload');
    if(formElements.length == 0) return;
    const form = formElements[0];

    form.addEventListener('submit', (event) => {
        event.preventDefault();

        const formData = new FormData();
        formData.append('caption', document.getElementById('postCaption').value);
        for(const id in images){
            formData.append('files[]', images[id], images[id].name);            
        }
        
        event.target.formData = formData;
        event.target.submit();
    });
});

// Inits the post when there is one
initAllPosts();