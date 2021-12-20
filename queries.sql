SELECT ItemId, COUNT(BidAmount) AS Bids 
FROM `BidRecord`
GROUP BY ItemId
--Find number of bids

SELECT ItemId, MAX(BidAmount) AS Bid, COUNT(BidAmount) AS Bids
FROM `BidRecord` 
GROUP BY ItemId
--Find max bid, bid count and itemId

SELECT ItemId, ItemName,Description,Cond_,Category,StartingBid,AuctionEndDateTime
FROM `Item` 
WHERE AuctionEndDateTime >= CURRENT_TIMESTAMP
--Display auctions that are still available 

SELECT i.ItemId, ItemName,Description,Cond_,Category,StartingBid,AuctionEndDateTime, b.HighestBid,b.BidsCount
FROM Item i
LEFT JOIN bidssummary b on i.ItemId = b.ItemId
WHERE AuctionEndDateTime >= CURRENT_TIMESTAMP
--display in the browse page

SELECT ItemId, ImageLocation
FROM Image
WHERE ImageId IN (SELECT min(ImageId) 
                 FROM Image
                 GROUP BY ItemId)
--Only selecting the first image

--browse
SELECT i.ItemId, ItemName,Description,Cond_,Category,StartingBid,ReservePrice,AuctionEndDateTime, b.HighestBid,b.BidsCount,f.ImageLocation
FROM Item i
LEFT JOIN (SELECT ItemId, MAX(BidAmount) AS HighestBid, COUNT(BidAmount) AS BidsCount
FROM `BidRecord` 
GROUP BY ItemId) b on i.ItemId = b.ItemId
INNER JOIN (SELECT ItemId, ImageLocation
FROM Image
WHERE ImageId IN (SELECT min(ImageId) 
                 FROM Image
                 GROUP BY ItemId)) f on i.ItemId = f.ItemId
WHERE AuctionEndDateTime >= CURRENT_TIMESTAMP
--browse

--browse with max price
SELECT i.ItemId, ItemName,Description,Cond_,Category,Greatest(StartingBid,COALESCE(b.HighestBid,0)) as Price,AuctionEndDateTime, COALESCE(b.BidsCount,0) as BidsCount,f.ImageLocation
FROM Item i
LEFT JOIN (SELECT ItemId, MAX(BidAmount) AS HighestBid, COUNT(BidAmount) AS BidsCount
FROM `BidRecord` 
GROUP BY ItemId) b on i.ItemId = b.ItemId
INNER JOIN (SELECT ItemId, ImageLocation
FROM Image
WHERE ImageId IN (SELECT min(ImageId) 
                 FROM Image
                 GROUP BY ItemId)) f on i.ItemId = f.ItemId
WHERE AuctionEndDateTime >= CURRENT_TIMESTAMP
--browse with max price

--search 
SELECT * FROM `browse` 
WHERE ItemName LIKE '%.'$das'%'
--search 

--choose category
SELECT * FROM `browse` 
WHERE Category = 'Living Room'
--choose category

--order by price
SELECT * FROM `browse` 
ORDER BY Price
--order by price 

--order by Auction end time 
SELECT * FROM `browse` 
ORDER BY AuctionEndDateTime
--order by Auction end time 

--query when open an from browse
SELECT i.ItemId, ItemName,Description,Cond_,Category,Greatest(StartingBid,COALESCE(b.HighestBid,0)) as Price,AuctionEndDateTime, COALESCE(b.BidsCount,0) as BidsCount
FROM Item i
LEFT JOIN (SELECT ItemId, MAX(BidAmount) AS HighestBid, COUNT(BidAmount) AS BidsCount
FROM `BidRecord` 
GROUP BY ItemId) b on i.ItemId = b.ItemId
WHERE AuctionEndDateTime >= CURRENT_TIMESTAMP AND i.ItemId = '10'

--select all images with id 10
SELECT ImageLocation 
FROM `Image` 
WHERE ItemId = '10'

--select all bids with id 10 
SELECT BidAmount, BidDateTime 
FROM BidRecord
WHERE ItemId = '10'

--bid on item 
--need to check price and userid==userid "cannot bid on your own item"
--then 
INSERT INTO `BidRecord` (`BidRecordId`, `ItemId`, `UserId`, `BidAmount`, `BidDateTime`) VALUES (NULL, '10', '5', '55', CURRENT_TIMESTAMP);

--bid results



--show all item which user 5 bided
SELECT DISTINCT ItemId
FROM `BidRecord` 
WHERE UserId = '5'

--show all items which user 5 bidded
SELECT * 
FROM `browse` 
WHERE ItemId in (SELECT DISTINCT ItemId
FROM `BidRecord` 
WHERE UserId = '5')

--my bids for user 5
SELECT i.ItemName,Max(BidAmount) as UserMaxBid, HighestBid,i.AuctionEndDateTime
FROM `BidRecord` b
INNER JOIN (SELECT ItemId, max(BidAmount) as HighestBid
FROM `BidRecord` 
GROUP BY ItemId) h on b.ItemId = h.ItemId
INNER JOIN (SELECT ItemName, ItemId, AuctionEndDateTime
FROM `Item`) i on b.ItemId = i.ItemId
WHERE UserId = 5
GROUP BY b.ItemId



-- select all other users that are not current max bidder 
SELECT DISTINCT(UserId) 
FROM BidRecord 
WHERE ItemID = '$item_id' 
AND UserId!='$user_id'";

--Max bid acount of each item in bidrecord
SELECT ItemId, MAX(BidAmount)
    FROM BidRecord
    GROUP BY ItemId
