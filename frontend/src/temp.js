
const markdown = `
# Heading 1
This is some **bold** text and some _italic_ text.

- Unordered list item 1
- Unordered list item 2

1. Ordered list item 1
2. Ordered list item 2

[Link to example.com](https://example.com)

![Alt text](https://example.com/image.jpg)
`;


// try {
    //   const existingAsset = await environment.getAsset(imgFileName);
    //   console.log('Asset already exists:', existingAsset);
    //   // Manejar el caso en el que el archivo ya existe
    // } catch (error) {
    //   // Continuar con la creación del asset
    // }

client.getSpace(spaceId) // Replace 'your-space-id' with your actual space ID
.then((space) => space.getEnvironment('master')) // or your environment ID if not 'master'
.then((environment) => environment.getAssets())
.then((response) => {
    console.log(response.items); // This will log all assets
})
.catch(console.error);

 // Fetch all categories
  // client.getSpace('xc32zku7u63j')
  // .then(space => space.getEnvironment('master'))
  // .then(environment => environment.getEntries({
  //   content_type: 'category'
  // }))
  // .then(response => {
  //   const categories = response.items;
  //   console.log('All categories:');
  //   categories.forEach(category => {
  //     console.log(category)
  //     // console.log(`- ${category.fields.title['en-US']}`);
  //   });
  // })
  // .catch(console.error);

  // client.getSpace('xc32zku7u63j')
  // .then(space => space.getEnvironment('master'))
  // .then(environment => environment.getContentType('article'))
  // .then(contentType => {
  //   console.log('Fields of the "article" content type:');
  //   console.log(contentType.fields)
  //   contentType.fields.forEach(field => {
  //     console.log(`- ${field.id} (${field.type})`);
  //   });
  // })
  // .catch(console.error);

    //get all published articles
    client.getSpace('xc32zku7u63j')
    .then((space) => space.getEnvironment('master'))
    .then((environment) => environment.getPublishedEntries({'content_type': 'article'})) // you can add more queries as 'key': 'value'
    .then((response) => console.log(response.items))
    .catch(console.error)
