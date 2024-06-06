global.process = {
  env: { NODE_ENV: 'production' },
  cwd: () => {}
};

const contentful_management = require('contentful-management');
const { richTextFromMarkdown } = require('@contentful/rich-text-from-markdown');
const TurndownService = require('turndown').default;
// const turndownService = new TurndownService();

const client = contentful_management.createClient({
  accessToken: 'accessToken'
});

const spaceId = 'spaceId';

const turndownService = new TurndownService({
  headingStyle: 'atx',
  rules: {
    'heading5': {
      filter: 'h5',
      replacement: (content) => `#### ${content}`
    }
  }
});

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
  var convertBtn = document.getElementById('convertBtn');
  const toast_success = document.getElementById('toast-success');
  const toast_failed = document.getElementById('toast-danger');

  const loadingIndicator = convertBtn.querySelector('svg');

  // Get all the checkboxes on the page
  const checkboxes = document.querySelectorAll('input[type="checkbox"]');
  // Filter the checkboxes to get only the ones that are checked
  const checkedCheckboxes = Array.from(checkboxes).filter(checkbox => checkbox.checked);

  var articleIdList = [];
  checkedCheckboxes.forEach(checkbox => {
    const id = checkbox.id.replace('checkbox-', '');
    // console.log(`Checked checkbox: ${id}`);
    articleIdList.push(id);
    return;
  });
  const filterArticles = article_data_json.filter(data => articleIdList.includes(data.id));

  filterArticles.forEach(async article => {
    loadingIndicator.classList.remove('hidden');
    let assetId = '';
    let richText = null;
    try {
      const markdown = turndownService.turndown(article.full_text);
      richText = await richTextFromMarkdown(markdown, {});
    } catch (error) {
      alert('Create Article was failed Because MarkDown Function');
      loadingIndicator.classList.add('hidden');
      toast_failed.classList.remove('hidden');
      return false;
    }
    
    try {
      const space = await client.getSpace(spaceId);
      const environment = await space.getEnvironment('master');
      
      //File name
      const url = new URL(article.image_url);
      const imgFileName = url.pathname.split('/').pop();

      // Create an asset in Contentful
      const asset = await environment.createAsset({
        fields: {
          title: {
            'it': article.image_text
          },
          file: {
            'it': {
              contentType: 'image/jpeg',
              fileName:  imgFileName ,
              upload: article.image_url
            }
          }
        }
      });

      // Process and publish the asset
      await asset.processForAllLocales();
      assetId = asset.sys.id;

      //publish asset
      const asset1 = await environment.getAsset(assetId);
      const publishedAsset = await asset1.publish();
      console.log('Asset published:', publishedAsset);

      const urlParts = article.slug.replace('https://www.costasmeralda.it/', '');
      const slugURL = urlParts.replace('/', '');
      let category_name = article.category_new;
      let categoryName = category_name.split(/\s|\&/).map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()).join(category_name.includes('&') ? ' & ' : ' ');
      if(category_name == 'ART & CULTURE') {
        categoryName = 'Art & Culture';
      }

      //get category id for category Name from contentful
      const categoryId = await environment.getEntries({
        content_type: 'category',
        'fields.name.it': categoryName
      })
      .then(response => {
        if (response.items.length > 0) {
          return response.items[0].sys.id;
        } else {
          return ''; // Category not found
        }
      })
      .catch(console.error);

      if(!categoryId) {
        return false;
      }

      //create new article and publish
      environment.createEntry('article', {
        fields: {
          title: {
            'it' : article.title,
          },
          date: {
            'it': article.date
          },
          slugURL : {
            'it' : slugURL
          },
          category: {
            'it': [{
              sys: {
                type: 'Link',
                linkType: 'Entry',
                id: categoryId
              }
            }]
          },
          coverMedia : {
            'it' : {
              sys: {
                type: 'Link',
                linkType: 'Asset',
                id: assetId
              }
            }
          },
          authorsLabel: {
            'it' : 'Redazione'
          }, 
          subtitle: {
            'it': ''
          },
          bodyText: {
            'it': richText
          },
          thumbFocus: {
            'it' : 'face'
          }
        },
      })
      .then((entry) => {
        console.log('Entry created:', entry);
        loadingIndicator.classList.add('hidden');
        toast_success.classList.remove('hidden');
        handleHideArticle(article.id);
        // return entry.publish();
      })
      // .then((publishedEntry) => {
      //   console.log('Entry published:', publishedEntry);
      //   loadingIndicator.classList.add('hidden');
      //   toast_success.classList.remove('hidden');
      //   handleHideArticle(article.id);
      // })
      .catch((error) => {
        loadingIndicator.classList.add('hidden');
        toast_failed.classList.remove('hidden');
        console.log('Error:', error);
      });
    } catch (error) {
      loadingIndicator.classList.add('hidden');
      toast_failed.classList.remove('hidden');
      console.log('Error create article:', error);
    }
  });
});

//ajax request
function handleHideArticle(id) {
  var xhr = new XMLHttpRequest();
  // Define the AJAX request
  xhr.open('POST', 'ajax.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  // Define the callback function when the request is complete
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
      console.log(xhr.responseText); // Display the response from the server
      var e1 = document.getElementById('article_' + id);
      var e2 = document.getElementById('default-modal-' + id);
      e1.remove();
      e2.remove();
    }
  };

  // Send the POST request with data
  xhr.send('id=' + id);
}

