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
      accessToken: 'dH3_18uD44VF8xD50QaFz6lD-D6LMa7uvKyqvOqNuFs',
      space: 'xc32zku7u63j'
    });

    client.getSpace()
    .then((space) => space.getEnvironment('master'))
    .then((environment) => environment.createEntry('article', {
      fields: {
        title: {
          'en-US': 'Title of Your New Article'
        },
        body: {
          'en-US': 'Content of your article goes here...'
        },
        // Add other fields as necessary
      }
    }))
    .then((entry) => {
      console.log(entry);
      return entry.publish();  // Optionally publish the entry
    })
    .catch(console.error);
});

