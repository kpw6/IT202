create table CompetitionParticipants (
   user_id int,
   comp_id int,
   created timestamp default CURRENT_TIMESTAMP,
   FOREIGN KEY (user_id) REFERENCES Users(id),
   FOREIGN KEY (comp_id) REFERENCES Competitions(id)
   )