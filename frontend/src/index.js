const contentful = require('contentful-management');

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
    const client = contentful.createClient({
        accessToken: 'Bearer dH3_18uD44VF8xD50QaFz6lD-D6LMa7wuvKyqvOqNuFs',
        space: 'xc32zku7u63j'
      });
      
      client.getSpace('xc32zku7u63j')
      .then((space) => space.getEnvironment('master'))
      .then((environment) => environment.getEntries())
      .then((entries) => {
        console.log(entries.items);
      })
      .catch((error) => {
        if (error.status === 404) {
          console.error('Space or environment not found');
        } else if (error.status === 401) {
          console.error('Invalid access token');
        } else {
          console.error('Error retrieving entries:', error);
        }
      });

    // // Get all entries
    // client.getSpace('xc32zku7u63j')
    // .then((space) => space.getEnvironment('master'))
    // .then((environment) => environment.getEntries())
    // .then((entries) => {
    //   // Process the entries
    //   entries.items.forEach((entry) => {
    //     console.log(entry.fields.title['en-US']);
    //   });
    // })
    // .catch(console.error);

    // client.getSpace()
    // .then((space) => space.getEnvironment('master'))
    // .then((environment) => environment.createEntry('article', {
    //   fields: {
    //     title: {
    //       'en-US': 'Title of Your New Article'
    //     },
    //     body: {
    //       'en-US': 'Content of your article goes here...'
    //     },
    //     // Add other fields as necessary
    //   }
    // }))
    // .then((entry) => {
    //   console.log(entry);
    //   return entry.publish();  // Optionally publish the entry
    // })
    // .catch(console.error);
});

