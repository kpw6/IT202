CREATE TABLE Scores
(
    id      int auto_increment,
    user_id int,
    score   int,
    name varchar(30) not null unique,
    created TIMESTAMP default current_timestamp,
    primary key (id),
    foreign key (user_id) references Users (id),
    foreign key (name) references Users (username)
)