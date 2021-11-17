SELECT familyName FROM laureates WHERE familyName <> 'org' GROUP BY familyName HAVING COUNT(*)>=5;
