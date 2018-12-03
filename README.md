# [KotN VI Website](https://UoMEsports.co.uk)
This is the final state of the KotN VI website after the event had concluded in December 2018. Git history is not present here, but we've open sourced and archived it as is, so that hopefully someone can make use of where we're up to, and perhaps learn something.

## Functionality
- Handles player registration and validates student status automatically
- Allows players to create teams and invite other valid users
- Allows teams to register for a certain qualifier once they are valid

## History
This website is very loosely based of the EssentialsTF website, which Dan Shields was lead developer of for some time. It started out as the website for the Rocket League Open tournament from November 2017, then was updated for us on KotN 2018. Most of the changes between then and now after KotN VI is a new frontend style, with some backend updates such as password resets.

## Future plans
The next version of the KotN website (under whatever event name it may be) will have a totally new frontend, and the API of this project will be rewritten into something like Laravel. The biggest weakness of this codebase is that page delivery is integrated in the same space as backend functions/API.
