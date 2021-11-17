SELECT COUNT(DISTINCT awardYear) 
FROM nobelPrizes P, awarded A, laureates L 
WHERE A.nobelKey=P.nobelKey AND A.id=L.id AND L.gender='org' 
GROUP BY category 
HAVING COUNT(category)>=1;
