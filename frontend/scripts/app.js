document.getElementById('table-search').addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
        // Get the search query value
        var searchQuery = this.value;

        // Redirect to the page with the search query parameter
        window.location.href = '?search_query=' + encodeURIComponent(searchQuery);
    }
});


document.getElementById('createJsonBtn').addEventListener('click', function() {
    fetch('createjson.php', {
        method: 'POST'
    })
    .then(response => {
        if (response.ok) {
            return response.blob();
        } else {
            throw new Error('Failed to create JSON file');
        }
    })
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'data.json';
        document.body.appendChild(a);
        a.click();
        a.remove();
    })
    .catch(error => {
        console.error(error);
        alert('An error occurred while creating the JSON file');
    });
});

//convert button
document.getElementById('convertBtn').addEventListener('click', function() {
    console.log("Convert Button");

    const client = contentful.createClient({
        space: 'xc32zku7u63j',
        accessToken: 'dH3_18uD44VF8xD50QaFz6lD-D6LMa7uvKyqvOqNuFs'
      });

    // Fetch all entries
    client.getEntries()
    .then((response) => {
        // Get the array of entries
        const allEntries = response.items;

        // Log the entries to the console
        console.log(allEntries);
    })
    .catch((error) => {
        console.log('Error occurred while fetching entries', error);
    });

    client.getEntries({
      content_type: 'article', // replace with the actual content type ID
      'sys.id': '22iUJRnEOfi7gVYim2dISO' // replace with the actual entry ID
    })
    .then((response) => {
      console.log(response.items); // this will log the entire entry data
    })
    .catch((error) => {
      console.log('Error occurred while fetching entries', error);
    });

    async function createArticle() {
      try {
        const space = await client.getSpace();
        console.log(space); 
        const environment = await space.getEnvironment();
    
        const entry = await environment.createEntry('article', {
          fields: {
            title: {
              'en-US': 'Legacy Article Title'
            },
            body: {
              'en-US': 'This is the body of the legacy article.'
            }
            // Add your content type fields here
          }
        });
    
        console.log('Entry created:', entry);
    
        // Optionally, publish the entry
        const publishedEntry = await entry.publish();
        console.log('Entry published:', publishedEntry);
    
      } catch (error) {
        console.log('Error creating entry:', error);
      }
    }
      
    createArticle();
   
    // client.createEntry('article', newEntry)
    // .then((entry) => {
    //   console.log('New entry created:', entry.sys.id);
    // })
    // .catch((error) => {
    //   console.log('Error creating entry:', error);
    // });
});