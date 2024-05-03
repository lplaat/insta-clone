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

        if(!post['isCreator']) {
            let followButton = post['element'].getElementsByClassName('follow-button')[0];
            followButton.onclick = () => {followUserButton(followButton, id)};
        }
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

function followUserButton(element, id){
    // Sets the new icon
    posts[id]['isFollowed'] = !posts[id]['isFollowed'];
    if(posts[id]['isFollowed']) {
        element.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon is-medium" viewBox="0 0 448 512"><path fill="#FF0000" d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg>';
    }else {
        element.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon is-medium" viewBox="0 0 448 512"><path fill="#03BE03" d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z"/></svg>';
    }

    // Sends the follow action to the server
    fetch("/user/" + posts[id]['creatorName'] + '/follow', {
        method: "POST",
        body: JSON.stringify({ following: posts[id]['isFollowed'] }),
        headers: {
            "Content-type": "application/json; charset=UTF-8"
        }
    });
}

let token = '';
let noMorePosts = false;
let loadingPosts = false;
async function loadPosts(){
    // Check if were not already loading posts
    if(loadingPosts) return;
    loadingPosts = true;
    
    // Load in posts from the feed end-point
    let response = await fetch("/feed?token=" + token, {
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
    loadPosts();

    // Event listener to check to load more posts
    window.addEventListener('scroll', function() {
        let scrollPosition = window.scrollY || document.documentElement.scrollTop;
        let totalHeight = document.documentElement.scrollHeight;
        let windowHeight = window.innerHeight;
  
        if (scrollPosition + windowHeight >= totalHeight - 5000) {
            loadPosts();
        }
    });
}

// Inits the post when there is one
initAllPosts();