import crawler from '@pierreminiggio/tiktok-video-lister'

crawler(process.argv[2], 2000).then(links => console.log(JSON.stringify(links)))